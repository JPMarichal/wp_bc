#!/usr/bin/env bash
# populate-persona.sh
# Busca datos biográficos de una persona en el corpus local y completa su CPT en WordPress.
# Dependencias: es (Everything), rg (ripgrep), wp-cli (en contenedor Docker)
# Modo de uso:
#   ./bin/populate-persona.sh <slug|ID|name>
# Ejemplos:
#   ./bin/populate-persona.sh michael-cziesla
#   ./bin/populate-persona.sh "Michael Cziesla"
#   ./bin/populate-persona.sh 3089

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
CORPUS_DIR="$SCRIPT_DIR/corpus"
CLI_CONTAINER="wp_bc_cli"
WP_CONTAINER="wp_bc"

# ─── helpers ──────────────────────────────────────────────────────────────────

die() { echo "❌ $*" >&2; exit 1; }
info() { echo "ℹ️  $*"; }
ok()   { echo "✅ $*"; }

# ─── crear script Python temporal en ruta Windows-compatible ─────────────────

make_py_script() {
    local prefix="${1:-bc-py}"
    local tmp_dir="$SCRIPT_DIR/tmp"
    # convertir a Windows path si es Unix-style (/c/... → C:/...)
    if [[ "$tmp_dir" == /[a-zA-Z]/* ]]; then
        tmp_dir=$(echo "$tmp_dir" | sed 's|^/\([a-zA-Z]\)/|\1:/|')
    fi
    mkdir -p "$tmp_dir"
    # nombre único
    local py_script="$tmp_dir/${prefix}-$$-$RANDOM.py"
    echo "$py_script"
}

# ─── argumento → post_id ──────────────────────────────────────────────────────

resolve_post_id() {
    local input="$1"
    # si es numérico, úsalo directo
    if [[ "$input" =~ ^[0-9]+$ ]]; then
        echo "$input"
        return
    fi
    # si es slug (sin espacios), busca por slug
    if [[ ! "$input" =~ \  ]]; then
        local id
        id=$(docker exec "$CLI_CONTAINER" wp post list \
            --post_type=bc_quote_author \
            --name="$input" \
            --field=ID 2>/dev/null) || true
        if [[ -n "$id" ]]; then
            echo "$id"
            return
        fi
    fi
    # busca por título
    local id
    id=$(docker exec "$CLI_CONTAINER" wp post list \
        --post_type=bc_quote_author \
        --search="$input" \
        --field=ID 2>/dev/null | head -1) || true
    if [[ -n "$id" ]]; then
        echo "$id"
        return
    fi
    die "No se encontró ningún post bc_quote_author para: $input"
}

# ─── slug desde post_id ───────────────────────────────────────────────────────

get_slug() {
    local post_id="$1"
    docker exec "$CLI_CONTAINER" wp post get "$post_id" --field=post_name 2>/dev/null
}

# ─── ubicar carpeta de la persona en el corpus ────────────────────────────────

find_corpus_dir() {
    local slug="$1"
    local dir="$CORPUS_DIR/personajes/$slug"
    # prueba directo
    if [[ -d "$dir" ]]; then
        echo "$dir"
        return
    fi
    # prueba con punto al final (algunos slugs WP tienen esa diferencia)
    if [[ -d "${dir}." ]]; then
        echo "${dir}."
        return
    fi
    # búsqueda flexible en personajes/
    local found
    # convertir slug para matching: "a-walter-stevenson" → también busca "a.-walter-stevenson"
    found=$(ls "$CORPUS_DIR/personajes/" 2>/dev/null | rg -i "^${slug}[.]?\$" | head -1) || true
    if [[ -z "$found" ]]; then
        # intentar con puntos después de iniciales (ej: a.-walter-stevenson)
        local dotted
        dotted=$(echo "$slug" | sed -E 's/\b([a-z])\b/\1./g; s/\.\././g')
        if [[ "$dotted" != "$slug" ]]; then
            found=$(ls "$CORPUS_DIR/personajes/" 2>/dev/null | rg -i "^${dotted}\$" | head -1) || true
        fi
    fi
    if [[ -n "$found" && -d "$CORPUS_DIR/personajes/$found" ]]; then
        echo "$CORPUS_DIR/personajes/$found"
        return
    fi
    # fallback: es con timeout de 3s
    found=$(es -timeout 3000 -n 1 -path "$CORPUS_DIR" "personajes\\$slug" 2>/dev/null | head -1) || true
    if [[ -n "$found" && -d "$found" ]]; then
        echo "$found"
        return
    fi
    die "No se encontró el directorio corpus para: $slug (buscado en $dir)"
}

# ─── normalizar path corpus (quitar trailing dot que rompe rg en Windows) ────

norm_corpus_path() {
    local path="$1"
    # si termina en ".", quítalo
    if [[ "$path" == *. ]]; then
        path="${path%.}"
    fi
    echo "$path"
}

# ─── extraer datos de ldsorg.html ─────────────────────────────────────────────

parse_ldsorg() {
    local html="$1"

    # usar python para extraer texto de párrafos (más robusto que regex)
    local py
    # evitar WindowsApps redirect que muestra mensaje de Microsoft Store
    for candidate in python python3; do
        local p
        p=$(command -v "$candidate" 2>/dev/null) || continue
        # saltar WindowsApps (no es Python real)
        if echo "$p" | rg -qi "windowsapps"; then
            continue
        fi
        py="$p"
        break
    done
    py="${py:-python}"

    # detectar si python existe realmente
    if ! "$py" --version >/dev/null 2>&1; then
        info "Python no disponible — no se pudo parsear ldsorg.html"
        echo "birth_date="
        echo "birth_place="
        echo "spouse_line="
        echo "description="
        echo "og_image="
        return
    fi

    # convertir ruta Unix → Windows para Python
    local win_html="$html"
    if [[ "$html" == /* ]]; then
        win_html=$(echo "$html" | sed 's|^/\([a-zA-Z]\)/|\1:/|')
    fi

    # escribir script Python a temp file (evita problemas de quoting)
    local py_script
    py_script=$(make_py_script "bc-parse")
    cat > "$py_script" << 'PYEOF'
import sys, re, html as h
with open(sys.argv[1], 'r', encoding='utf-8', errors='replace') as f:
    html_content = f.read()
data = {}
ps = re.findall(r'<p[^>]*>(.*?)</p>', html_content, re.DOTALL)
for p in ps:
    text = re.sub(r'<[^>]+>', '', p).strip()
    text = re.sub(r'\s+', ' ', text)
    if not text:
        continue
    if 'was born in' in text.lower():
        m = re.search(r'was born in (.*?),? on (.*?)\.', text, re.I)
        if m:
            data['birth_place'] = m.group(1).strip()
            data['birth_date_raw'] = m.group(2).strip()
    if 'married' in text.lower():
        m = re.search(r'He married (.*?) in (\d{4})', text)
        if m:
            data['spouse_name'] = m.group(1).strip()
            data['marriage_year'] = m.group(2)
        mc = re.search(r'parents of (\d+) children', text, re.I)
        if mc:
            data['children_count'] = mc.group(1)
    if 'was sustained as' in text.lower():
        m = re.search(r'as an?\s+(.*?)(?: of| at|\.|$)', text, re.I)
        if m:
            data['calling_en'] = m.group(1).strip()
m = re.search(r'og:image[^>]*content="([^"]+)"', html_content)
if m:
    data['og_image'] = m.group(1)
if not data.get('calling_en'):
    m = re.search(r'name="description"[^>]*content="([^"]+)"', html_content)
    if m:
        data['description_fallback'] = m.group(1)
dds = re.findall(r'<dd[^>]*class="Common__Def[^"]*"[^>]*>(.*?)</dd>', html_content, re.DOTALL)
for dd_text in dds:
    clean = h.unescape(re.sub(r'<[^>]+>', '', dd_text).strip())
    if re.match(r'\s*\d{1,2}\s+\w+\s+\d{4}\s*$', clean):
        if not data.get('birth_date_raw'):
            data['birth_date_raw'] = clean
    elif re.match(r'.*,\s*\w+', clean) and ',' in clean:
        if not data.get('birth_place'):
            data['birth_place'] = clean
out = []
for k, v in data.items():
    out.append(f'{k}={v}')
sys.stdout.write('|'.join(out))
PYEOF

    local py_out
    py_out=$("$py" "$py_script" "$win_html") || true
    rm -f "$py_script" 2>/dev/null

    # convertir salida de python a variables
    local birth_date_raw="" birth_place="" spouse_name="" marriage_year="" children_count="" calling_en="" og_image="" description_fallback=""
    IFS='|' read -ra pairs <<< "$py_out"
    for pair in "${pairs[@]}"; do
        local key="${pair%%=*}"
        local val="${pair#*=}"
        case "$key" in
            birth_date_raw)   birth_date_raw="$val" ;;
            birth_place)      birth_place="$val" ;;
            spouse_name)      spouse_name="$val" ;;
            marriage_year)    marriage_year="$val" ;;
            children_count)   children_count="$val" ;;
            calling_en)       calling_en="$val" ;;
            og_image)         og_image="$val" ;;
            description_fallback) description_fallback="$val" ;;
        esac
    done

    # construir spouse_line a partir de datos extraídos (para compatibilidad con el resto del script)
    local spouse_line=""
    if [[ -n "$spouse_name" ]]; then
        spouse_line="He married $spouse_name in $marriage_year"
        [[ -n "$children_count" ]] && spouse_line+=". They are the parents of $children_count children."
    fi

    local description="${calling_en:-$description_fallback}"

    echo "birth_date=$birth_date_raw"
    echo "birth_place=$birth_place"
    echo "spouse_line=$spouse_line"
    echo "description=$description"
    echo "og_image=$og_image"
}

# ─── extraer datos de wikipedia.html ──────────────────────────────────────────

parse_wikipedia() {
    local html="$1"
    # Check if the page exists (no "noarticletext")
    if cat "$html" | rg -q "noarticletext\|Wikipedia does not have" 2>/dev/null; then
        echo "wikipedia_exists=false"
        return
    fi
    echo "wikipedia_exists=true"
    # birth — busca infobox data
    local birth_info
    birth_info=$(cat "$html" | rg -oP '(?:Born|Nacimiento|Bürth)\s*</th>\s*<td[^>]*>\K[^<]+' | head -1) || true
    echo "birth_info=$birth_info"
}

# ─── mapear fechas en inglés a español ───────────────────────────────────────

translate_date() {
    local date_str="$1"
    # normalize whitespace (including non-breaking spaces \xc2\xa0)
    date_str=$(echo "$date_str" | sed 's/\xc2\xa0/ /g; s/[[:space:]]\{1,\}/ /g')
    # strip trailing dot after abbreviated month (e.g. "Feb." -> "Feb")
    date_str=$(echo "$date_str" | sed -E 's/([A-Za-z]{3,9})\.([[:space:]])/\1\2/g')
    # Full month names → español
    date_str=$(echo "$date_str" | sed \
        -e 's/January/enero/g' -e 's/February/febrero/g' \
        -e 's/March/marzo/g' -e 's/April/abril/g' \
        -e 's/May/mayo/g' -e 's/June/junio/g' \
        -e 's/July/julio/g' -e 's/August/agosto/g' \
        -e 's/September/septiembre/g' -e 's/October/octubre/g' \
        -e 's/November/noviembre/g' -e 's/December/diciembre/g')
    # 3-letter abbreviations
    date_str=$(echo "$date_str" | sed \
        -e 's/\bJan\b/enero/g'  -e 's/\bFeb\b/febrero/g' \
        -e 's/\bMar\b/marzo/g'  -e 's/\bApr\b/abril/g' \
        -e 's/\bMay\b/mayo/g'   -e 's/\bJun\b/junio/g' \
        -e 's/\bJul\b/julio/g'  -e 's/\bAug\b/agosto/g' \
        -e 's/\bSep\b/septiembre/g' -e 's/\bOct\b/octubre/g' \
        -e 's/\bNov\b/noviembre/g'  -e 's/\bDec\b/diciembre/g')
    # "26 julio 1972" → "26 de julio de 1972"
    date_str=$(echo "$date_str" | sed -E 's/([0-9]+) ([a-záéíóúñ]+) ([0-9]+)/\1 de \2 de \3/i')
    # "noviembre 2, 1962" → "2 de noviembre de 1962"
    date_str=$(echo "$date_str" | sed -E 's/([a-záéíóúñ]+) ([0-9]+),? ([0-9]+)/\2 de \1 de \3/i')
    # "1900-10-06" → "6 de octubre de 1900"
    date_str=$(echo "$date_str" | sed -E 's/^([0-9]{4})-0?([0-9]+)-0?([0-9]+)$/\3|\2|\1/' | sed \
        -e 's/|01|/|enero|/g'   -e 's/|1|/|enero|/g' \
        -e 's/|02|/|febrero|/g' -e 's/|2|/|febrero|/g' \
        -e 's/|03|/|marzo|/g'   -e 's/|3|/|marzo|/g' \
        -e 's/|04|/|abril|/g'   -e 's/|4|/|abril|/g' \
        -e 's/|05|/|mayo|/g'    -e 's/|5|/|mayo|/g' \
        -e 's/|06|/|junio|/g'   -e 's/|6|/|junio|/g' \
        -e 's/|07|/|julio|/g'   -e 's/|7|/|julio|/g' \
        -e 's/|08|/|agosto|/g'  -e 's/|8|/|agosto|/g' \
        -e 's/|09|/|septiembre|/g' -e 's/|9|/|septiembre|/g' \
        -e 's/|10|/|octubre|/g' \
        -e 's/|11|/|noviembre|/g' \
        -e 's/|12|/|diciembre|/g')
    date_str=$(echo "$date_str" | sed -E 's/([0-9]+)\|([a-z]+)\|([0-9]+)/\1 de \2 de \3/i')
    echo "$date_str"
}

# ─── fuentes secundarias: cn-bio-data.json ──────────────────────────────────

parse_cn_bio() {
    local name="$1"
    local json="$CORPUS_DIR/cn-bio-data.json"
    [[ -f "$json" ]] || return

    python -c "
import json, sys
with open('$json', 'r', encoding='utf-8') as f:
    data = json.load(f)
name_lower = '$name'.lower().strip()
for entry in data:
    ename = entry.get('name', '').lower().strip()
    if ename == name_lower or ename.startswith(name_lower):
        out = []
        if entry.get('birthYear'):
            sys.stdout.write(f'birth_date={entry[\"birthYear\"]}\n')
        if entry.get('deathYear'):
            sys.stdout.write(f'death_date={entry[\"deathYear\"]}\n')
        if entry.get('image'):
            sys.stdout.write(f'image_url={entry[\"image\"]}\n')
        return
" 2>/dev/null || true
}

# ─── fuentes secundarias: church-news/*.html ────────────────────────────────

parse_church_news() {
    local slug="$1"
    local html=""
    # generar variante con puntos tras iniciales (a-walter → a.-walter)
    local dotted
    dotted=$(echo "$slug" | sed -E 's/\b([a-z])\b/\1./g; s/\.\././g')
    # buscar en church-news/ con el slug
    for candidate in "$CORPUS_DIR/church-news/$slug.html" "$CORPUS_DIR/church-news/$slug..html" "$CORPUS_DIR/church-news/$dotted.html" "$CORPUS_DIR/church-news/$dotted..html"; do
        if [[ -f "$candidate" ]]; then
            html="$candidate"
            break
        fi
    done
    [[ -n "$html" ]] || return

    # convertir ruta Unix → Windows para Python
    local win_html="$html"
    if [[ "$html" == /* ]]; then
        win_html=$(echo "$html" | sed 's|^/\([a-zA-Z]\)/|\1:/|')
    fi

    local py
    for candidate in python python3; do
        local p
        p=$(command -v "$candidate" 2>/dev/null) || continue
        if echo "$p" | rg -qi "windowsapps"; then
            continue
        fi
        py="$p"
        break
    done
    py="${py:-python}"
    if ! "$py" --version >/dev/null 2>&1; then
        return
    fi

    # escribir script Python a temp file (evita problemas de quoting)
    local py_script
    py_script=$(make_py_script "bc-cn-parse")
    cat > "$py_script" << 'PYEOF'
import sys, re
with open(sys.argv[1], 'r', encoding='utf-8', errors='replace') as f:
    content = f.read()
data = {}
# born
m = re.search(r'was born\s+(\w+\.?\s+\d+,\s*\d{4})\s*,\s*in\s+([^.,]+(?:,\s*[^.,]+)?)', content, re.I)
if m:
    data['birth_date_raw'] = m.group(1).strip()
    data['birth_place'] = m.group(2).strip()
# death
m = re.search(r'died?\s+(?:of\s+[^,]+?)?\s*on\s+(\w+\.?\s+\d+,\s*\d{4})', content, re.I)
if m:
    data['death_date_raw'] = m.group(1).strip()
m2 = re.search(r'in\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\s+(at|on|after)', content)
if m2:
    data['death_place'] = m2.group(1).strip()
# spouse(s)
for m in re.finditer(r'He\s+married\s+([^,]+?)\s+in\s+(\d{4})', content, re.I):
    key = 'spouse_name' if 'spouse_name' not in data else 'spouse_name2'
    data[key] = m.group(1).strip()
    data[key.replace('name','year')] = m.group(2)
mc = re.search(r'They\s+had\s+(\d+)\s+children', content, re.I)
if mc:
    data['children_count'] = mc.group(1)
mc2 = re.search(r'survived\s+by.*?(\d+)\s+children', content, re.I)
if mc2:
    data['children_count'] = data.get('children_count') or mc2.group(1)
# calling
m = re.search(r'sustained\s+to\s+the\s+(.*?)(?:where|\.)', content, re.I)
if m:
    data['calling_en'] = m.group(1).strip()
# description (first paragraph)
m = re.search(r'<p[^>]*class="c-paragraph"[^>]*>(.*?)</p>', content, re.DOTALL)
if m:
    text = re.sub(r'<[^>]+>', '', m.group(1)).strip()
    text = re.sub(r'\s+', ' ', text)
    m2 = re.search(r'who\s+served\s+(.*?)\.', text, re.I)
    if m2 and 'calling_en' not in data:
        data['calling_en'] = m2.group(1).strip()
for k, v in data.items():
    sys.stdout.write(f'{k}={v}\n')
PYEOF

    "$py" "$py_script" "$win_html" 2>/dev/null || true
    rm -f "$py_script" 2>/dev/null
}

# ─── fuentes secundarias: chd.html (Church History Biographical Database) ───

parse_chd() {
    local html="$1"
    [[ -f "$html" ]] || return

    local win_html="$html"
    if [[ "$html" == /* ]]; then
        win_html=$(echo "$html" | sed 's|^/\([a-zA-Z]\)/|\1:/|')
    fi

    local py
    for candidate in python python3; do
        local p
        p=$(command -v "$candidate" 2>/dev/null) || continue
        if echo "$p" | rg -qi "windowsapps"; then
            continue
        fi
        py="$p"
        break
    done
    py="${py:-python}"
    if ! "$py" --version >/dev/null 2>&1; then
        return
    fi

    local py_script
    py_script=$(make_py_script "bc-chd-parse")
    cat > "$py_script" << 'PYEOF'
import sys, json, re
with open(sys.argv[1], 'r', encoding='utf-8', errors='replace') as f:
    html = f.read()
m = re.search(r'__NEXT_DATA__[^>]*>\s*(\{.*?\})\s*</script>', html, re.DOTALL)
if not m:
    sys.exit(0)
data = json.loads(m.group(1))
pp = data.get('props', {}).get('pageProps', {})
if not pp:
    sys.exit(0)

ps = pp.get('personSummary', {})
if not ps or ps.get('statusCode') == 404:
    sys.exit(0)

# timeline events
tl = pp.get('timeline', {})
for ev in tl.get('events', []):
    qual = ev.get('qualifier', '')
    sd = ev.get('startDate', {})
    date = sd.get('datePrimaryDate', '') if isinstance(sd, dict) else ''
    loc = ev.get('location', '') or (sd.get('location', '') if isinstance(sd, dict) else '')
    if qual == 'Birth':
        if date:
            sys.stdout.write(f'birth_date={date}\n')
        if loc:
            sys.stdout.write(f'birth_place={loc}\n')
    elif qual in ('Death', 'Died'):
        if date:
            sys.stdout.write(f'death_date={date}\n')
        if loc:
            sys.stdout.write(f'death_place={loc}\n')

# image
images = pp.get('images', {})
pi = images.get('primaryImage', {})
if isinstance(pi, dict) and pi.get('imageUri'):
    uri = pi['imageUri']
    if uri.startswith('/'):
        uri = 'https://history.churchofjesuschrist.org' + uri
    sys.stdout.write(f'og_image={uri}\n')

# spouse from relationship blocks
for ev in tl.get('events', []):
    for rb in ev.get('relationshipBlocks', []):
        for rel in rb.get('relationships', []):
            if isinstance(rel, dict) and rel.get('typeName'):
                tname = rel.get('typeName', '').lower()
                if 'spouse' in tname or 'married' in tname or 'wife' in tname or 'husband' in tname:
                    fname = rel.get('fullName', '')
                    if fname:
                        sys.stdout.write(f'spouse_name={fname}\n')
PYEOF

    "$py" "$py_script" "$win_html" 2>/dev/null || true
    rm -f "$py_script" 2>/dev/null
}

# ─── fuentes secundarias: biographical-encyclopedia (vol1-4 de Andrew Jenson) ──

parse_bio_encyclopedia() {
    local title="$1"
    [[ -z "$title" ]] && return

    local surname
    surname=$(echo "$title" | awk '{print $NF}' | tr '[:lower:]' '[:upper:]')
    local given
    given=$(echo "$title" | awk '{$NF=""; sub(/ +$/,""); print}')
    [[ -z "$surname" || -z "$given" ]] && return

    # Escapar espacios en given para rg: "George Quayle" → "George\s+Quayle"
    local given_rg
    given_rg=$(echo "$given" | sed 's/ /\\s+/g')

    # Buscar en qué volumen está la entrada
    local vol_file=""
    for vol in "$CORPUS_DIR/biographical-encyclopedia/vol1.txt" \
                "$CORPUS_DIR/biographical-encyclopedia/vol2.txt" \
                "$CORPUS_DIR/biographical-encyclopedia/vol3.txt" \
                "$CORPUS_DIR/biographical-encyclopedia/vol4.txt"; do
        [[ -f "$vol" ]] || continue
        if rg -n "^${surname},\s{2,}${given_rg}\b" "$vol" >/dev/null 2>&1; then
            vol_file="$vol"
            break
        fi
    done
    # Fallback: buscar OCR typos conocidos (p.ej. SNOAV por SNOW)
    if [[ -z "$vol_file" ]]; then
        local ocr_typo=""
        case "$surname" in
            SNOW) ocr_typo="SNOAV" ;;
            SMITH) ocr_typo="SM1TH" ;;
        esac
        if [[ -n "$ocr_typo" ]]; then
            for vol in "$CORPUS_DIR/biographical-encyclopedia/vol1.txt" \
                        "$CORPUS_DIR/biographical-encyclopedia/vol2.txt" \
                        "$CORPUS_DIR/biographical-encyclopedia/vol3.txt" \
                        "$CORPUS_DIR/biographical-encyclopedia/vol4.txt"; do
                [[ -f "$vol" ]] || continue
                if rg -n "^${ocr_typo},\s{2,}${given_rg}\b" "$vol" >/dev/null 2>&1; then
                    vol_file="$vol"
                    surname="$ocr_typo"
                    break
                fi
            done
        fi
    fi
    [[ -z "$vol_file" ]] && return

    # Convertir a Windows path para Python
    local win_vol="$vol_file"
    if [[ "$vol_file" == /* ]]; then
        win_vol=$(echo "$vol_file" | sed 's|^/\([a-zA-Z]\)/|\1:/|')
    fi

    local py
    for candidate in "$PROGRAMFILES/Python37-32/python.exe" "$PROGRAMFILES (x86)/Python37-32/python.exe" python python3; do
        local p
        p=$(command -v "$candidate" 2>/dev/null) || continue
        if echo "$p" | rg -qi "windowsapps"; then
            continue
        fi
        py="$p"
        break
    done
    py="${py:-python}"
    if ! "$py" --version >/dev/null 2>&1; then
        return
    fi

    local py_script
    py_script=$(make_py_script "bc-bioencyc-parse")

    cat > "$py_script" << 'PYEOF'
import sys, re

filepath = sys.argv[1]
surname = sys.argv[2].upper()
givenname = sys.argv[3]

with open(filepath, 'r', encoding='utf-8', errors='replace') as f:
    lines = f.readlines()

entry_start = None
given_parts = givenname.split()
given_pattern = r'\s+'.join(re.escape(p) for p in given_parts)
entry_re = re.compile(r'^' + re.escape(surname) + r',\s{2,}' + given_pattern + r',')
for i, line in enumerate(lines):
    if entry_re.match(line):
        entry_start = i
        break

if entry_start is None:
    sys.exit(0)

raw_lines = []
for i in range(entry_start, len(lines)):
    line = lines[i].rstrip('\n\r')
    if i > entry_start + 1 and re.match(r'^[A-Z][A-Z\']{2,20},\s{2,}[A-Z][a-z]', line):
        break
    raw_lines.append(line)

# Clean text for data extraction
text = '\n'.join(raw_lines)
text = re.sub(r'(?m)^BIOGRAPHICAL,\s*ENCYCLOPEDIA\.?\s*$', '', text)
text = re.sub(r'(?m)^LATTER-DAY\s+SAINT\s*$', '', text)
text = re.sub(r'(?m)^[A-Z\s]{10,}\d*\s*$', '', text)
text = re.sub(r'(?m)^\d+\s*$', '', text)
text = re.sub(r'(\w+)-\s*\n\s*(\w+)', r'\1\2', text)
text = re.sub(r'[ \t]+', ' ', text)
text = re.sub(r'\n\s*\n', '\n', text)
text = text.replace('\n', ' ')
text = re.sub(r'\s+', ' ', text).strip()

data = {}
months = r'(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\w*\.?'

# Parents: "His parents, Name and Name, were"
m = re.search(r'[Hh]is\s+parents,\s*([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*)\s+and\s+([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*)\s*,?\s+were', text)
if not m:
    # "the (son|daughter|child) of Name and Name"
    m = re.search(r'\b(?:the\s+)?(?:\w*?\s+)?\b(?:son|daughter|child)\b\s+of\s+([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*)\s+and\s+([A-Z][a-zA-Z]+(?:[-\s][A-Z][a-zA-Z]+)*)[\.;,\s]\s', text)
if m:
    father = m.group(1).strip()
    mother = m.group(2).strip()
    # Restore father's surname from subject's surname if father is single-word
    if ' ' not in father:
        father += ' ' + surname.capitalize()
    data['father'] = father
    data['mother'] = mother
    # Mother's maiden name via "née"
    mother_surname = mother.rsplit(' ', 1)[-1]
    m_nee = re.search(rf'{re.escape(mother_surname)}\s*,?\s*n[eé]e\s+([A-Z][a-zA-Z]+)', text)
    if m_nee:
        data['mother_maiden'] = m_nee.group(1)
else:
    mf = re.search(r'[Hh]is\s+father,\s*([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*)\s*,', text)
    if mf:
        father = mf.group(1).strip()
        if ' ' not in father:
            father += ' ' + surname.capitalize()
        data['father'] = father
    mm = re.search(r'[Hh]is\s+mother,\s*([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*)\s*,', text)
    if mm:
        mother = mm.group(1).strip()
        data['mother'] = mother
        m_nee = re.search(rf'{re.escape(mother.rsplit(" ", 1)[-1])}\s*,?\s*n[eé]e\s+([A-Z][a-zA-Z]+)', text)
        if m_nee:
            data['mother_maiden'] = m_nee.group(1)

# Birth (replace common abbreviations so "St. Clair" isn't split at period)
def _abbrev_fix(t):
    return re.sub(r'\b(St|Mt|Ft|Mr|Mrs|Ms|Dr|No|Jr|Sr|Co|De|La)\.', lambda x: f'\x00AB{x.group(1)}\x00', t)
def _abbrev_restore(t):
    return re.sub(r'\x00AB(\w+)\x00', r'\1.', t)

# Clean up place name: strip "his home in" prefix and trailing descriptive text
def clean_place(p):
    p = re.sub(r'^(?:(?:his|the)\s+)?(?:home|residence)\s+(?:in|at|near)\s+', '', p, flags=re.I)
    p = re.sub(r',\s+(?:truly|en\s+route|while|aged|being|at\s+the\s+age).*$', '', p, flags=re.I)
    return p.strip().rstrip(',')

temp_text = _abbrev_fix(text)
# Limit scope to first 1500 chars to avoid matching other entries within this block
scope_text = temp_text[:1500]
m = re.search(r'(?:was\s+)?born\s+(?:on\s+\w+,\s*)?(' + months + r'\s+\d{1,2}[,.]?\s*[0-9S]{4})\s*,?\s*(?:at|in)\s+(.+?)(?:\.\s+[A-Z]|\.\s*$|;)', scope_text)
if m:
    data['birth_date'] = _abbrev_restore(m.group(1).strip().rstrip(','))
    data['birth_place'] = _abbrev_restore(m.group(2).strip().rstrip(','))

# Death — "He died", "President X died", "He departed this life", etc.
# only the subject's own death, not "his father died" etc.
death_kw = r'(?:died|departed\s+this\s+life|passed\s+away)'
death_yr = r'[0-9S]{4}'
death_day = r'\d{1,2}[,.]?\s*'
scope_text_d = temp_text[:2000]
# Skip "his home in/at" etc. before the place name
home_prefix = r'(?:(?:his|the)\s+)?(?:home|residence)\s+(?:in|at|near)\s+'
# Pattern A: "... died on DATE, in/at PLACE." (date → g1, place → g2)
m = re.search(r'\b(?:He|he|President\s+\w+|Elder\s+\w+)\s+' + death_kw + r'\s+.*?(' + months + r'\s+' + death_day + death_yr + r')\s*,?\s*(?:at|in)\s+(?:' + home_prefix + r')?(.+?)(?:\.\s+[A-Z]|\.\s*$|;)', scope_text_d)
if m:
    data['death_date'] = _abbrev_restore(m.group(1).strip().rstrip(','))
    data['death_place'] = clean_place(_abbrev_restore(m.group(2).strip().rstrip(',')))
else:
    # Pattern B: "... departed this life at PLACE, DATE." (place → g1, date → g2)
    m = re.search(r'\b(?:He|he|President\s+\w+|Elder\s+\w+)\s+' + death_kw + r'\s+.*?(?:at|in)\s+(?:' + home_prefix + r')?(.+?),\s*(' + months + r'\s+' + death_day + death_yr + r')(?:\.\s+[A-Z]|\.\s*$|;)', scope_text_d)
    if m:
        data['death_place'] = clean_place(_abbrev_restore(m.group(1).strip().rstrip(',')))
        data['death_date'] = _abbrev_restore(m.group(2).strip().rstrip(','))
    else:
        # Pattern C: "DATE when he died in PLACE." (date → g1, place → g2)
        m = re.search(r'(' + months + r'\s+' + death_day + death_yr + r')\s*,?\s*when\s+he\s+' + death_kw + r'\s*(?:at|in)\s+(?:' + home_prefix + r')?(.+?)(?:\.\s+[A-Z]|\.\s*$|;)', scope_text_d)
        if m:
            data['death_date'] = _abbrev_restore(m.group(1).strip().rstrip(','))
            data['death_place'] = clean_place(_abbrev_restore(m.group(2).strip().rstrip(',')))

# Marriage (greedy name capture for multi-word names)
m = re.search(r'married\s+(?:Miss\s+|Sister\s+)?([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*)\s+in\s+(\d{4})', text)
if m:
    data['spouse_name'] = m.group(1).strip()
    data['spouse_year'] = m.group(2)
else:
    m = re.search(r'(\d{4}),\s*(?:he|and)\s+married\s+(?:Miss\s+|Sister\s+)?([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+)*)(?:\s|\.|,|$)', text)
    if m:
        data['spouse_year'] = m.group(1)
        data['spouse_name'] = m.group(2).strip()

# Children
m = re.search(r'(?:father|parents?)\s+of\s+(\d+)\s+children', text, re.I)
if not m:
    m = re.search(r'had\s+(\d+)\s+children', text, re.I)
if m:
    data['children_count'] = m.group(1)

# Calling — collect continuous header lines (stop at first gap or artifact)
header_lines = []
for line in raw_lines:
    stripped = line.strip()
    # Stop at blank line or page-break artifact
    if not stripped:
        break
    if re.match(r'^\d+\s*$', stripped):
        break
    if re.match(r'^[A-Z\s]{10,}\d*\s*$', stripped) and len(stripped) >= 5:
        break
    if re.match(r'^BIOGRAPHICAL,\s*ENCYCLOPEDIA\.?\s*$', stripped, re.I):
        break
    if re.match(r'^LATTER-DAY\s+SAINT\s*$', stripped, re.I):
        break
    # Stop at line containing "born" (header end marker)
    if re.search(r'\b(?:was\s+)?born\b', stripped):
        header_lines.append(line)
        break
    header_lines.append(line)

calling_text = ' '.join(line.strip() for line in header_lines)
calling_text = re.sub(r'(\w+)-\s+(\w+)', r'\1\2', calling_text)

rest = re.sub(r'^[A-Z\']+,\s+', '', calling_text)
parts = rest.split(',')
start_idx = 2 if len(parts) >= 2 and re.match(r'^\s*(?:junior|senior|jr\.?|sr\.?)\s*$', parts[1], re.I) else 1
calling_text = ','.join(parts[start_idx:]).strip()
calling_text = re.sub(r'\s+was\s+born.*$', '', calling_text)
calling_text = re.sub(r'\s+born\b.*$', '', calling_text)
# Strip biographical patterns: "was the first son of", "was a son of", etc.
calling_text = re.sub(r'\s+was\s+(?:(?:the\s+)?\w*?\s+)?(?:son|daughter|child)\s+of\b.*$', '', calling_text)
calling_text = calling_text.strip().rstrip(',')
if calling_text:
    data['calling_en'] = calling_text

for k, v in data.items():
    if v:
        sys.stdout.write(f'{k}={v}\n')
PYEOF

    "$py" "$py_script" "$win_vol" "$surname" "$given" 2>/dev/null || true
    rm -f "$py_script" 2>/dev/null
}

# ─── fuentes secundarias: historical-conf.html (Historical General Conferences) ─

parse_historical_conf() {
    local html="$1"
    [[ -f "$html" ]] || return

    local win_html="$html"
    if [[ "$html" == /* ]]; then
        win_html=$(echo "$html" | sed 's|^/\([a-zA-Z]\)/|\1:/|')
    fi

    local py
    for candidate in python python3; do
        local p
        p=$(command -v "$candidate" 2>/dev/null) || continue
        if echo "$p" | rg -qi "windowsapps"; then
            continue
        fi
        py="$p"
        break
    done
    py="${py:-python}"
    if ! "$py" --version >/dev/null 2>&1; then
        return
    fi

    local py_script
    py_script=$(make_py_script "bc-historical-parse")
    cat > "$py_script" << 'PYEOF'
import sys, re, json
with open(sys.argv[1], 'r', encoding='utf-8', errors='replace') as f:
    html = f.read()
data = {}
# og tags
m = re.search(r'<meta\s+property="og:description"\s+content="([^"]+)"', html)
if m:
    data['calling_en'] = m.group(1)
m = re.search(r'<meta\s+property="og:image"\s+content="([^"]+)"', html)
if m and 'og_image' not in data:
    data['og_image'] = m.group(1)
# extract all paragraph content (can be multiple divs)
text = ''
for m in re.finditer(r'<div class="paragraph">(.*?)</div>', html, re.DOTALL):
    text += m.group(1) + ' '
if not text.strip():
    sys.exit(0)
# strip HTML tags
text = re.sub(r'<[^>]+>', '', text)
text = re.sub(r'&rsquo;', "'", text)
text = re.sub(r'&mdash;', '---', text)
text = re.sub(r'&nbsp;', ' ', text)
text = re.sub(r'&#8203;', '', text)
text = re.sub(r'&frac12;', '1/2', text)
text = re.sub(r'&bdquo;', ',', text)
text = re.sub(r'\s+', ' ', text).strip()
# marriage pattern
mm = re.search(r'was\s+married\s+to\s+([A-Za-z\s]+?)\s+of\s+([A-Za-z\s]+?)\s+in\s+the\s+[A-Za-z\s]+?\s+in\s+(\d{4})', text, re.I)
if mm:
    data['spouse_name'] = mm.group(1).strip()
    data['spouse_year'] = mm.group(3)
# children (supports both "4 children" and "four children")
words = {'one':'1','two':'2','three':'3','four':'4','five':'5','six':'6','seven':'7','eight':'8','nine':'9','ten':'10'}
mc = re.search(r'parents\s+of\s+(\d+|' + '|'.join(words.keys()) + r')\s+children', text, re.I)
if mc:
    val = mc.group(1).lower()
    data['children_count'] = words.get(val, val)
# birth from text (pattern: "born in PLACE, MONTH DAY, YEAR")
mb = re.search(r'was\s+born\s+in\s+([A-Za-z\s.]+?),?\s+([A-Za-z]+)\s+(\d{1,2}),?\s+(\d{4})', text, re.I)
if mb:
    data['birth_place'] = mb.group(1).strip().rstrip(',').rstrip()
    data['birth_date_raw'] = f'{mb.group(2)} {mb.group(3)}, {mb.group(4)}'
for k, v in data.items():
    sys.stdout.write(f'{k}={v}\n')
PYEOF

    "$py" "$py_script" "$win_html" 2>/dev/null || true
    rm -f "$py_script" 2>/dev/null
}

# ─── fuentes secundarias: ensign-bio.txt (Ensign magazine biography) ────────

parse_ensign_bio() {
    local txt="$1"
    [[ -f "$txt" ]] || return

    local win_txt="$txt"
    if [[ "$txt" == /* ]]; then
        win_txt=$(echo "$txt" | sed 's|^/\([a-zA-Z]\)/|\1:/|')
    fi

    local py
    for candidate in python python3; do
        local p
        p=$(command -v "$candidate" 2>/dev/null) || continue
        if echo "$p" | rg -qi "windowsapps"; then
            continue
        fi
        py="$p"
        break
    done
    py="${py:-python}"
    if ! "$py" --version >/dev/null 2>&1; then
        return
    fi

    local py_script
    py_script=$(make_py_script "bc-ensign-parse")
    cat > "$py_script" << 'PYEOF'
import sys, re
with open(sys.argv[1], 'r', encoding='utf-8', errors='replace') as f:
    text = f.read()
data = {}
# birth: "was born in Salt Lake City, Utah, to Parents on 2 May 1945"
m = re.search(r'was born in ([^,]+(?:,\s*[^,]+?)?), to [^,]+,? on (\d{1,2} \w+ \d{4})', text, re.I)
if m:
    data['birth_place'] = m.group(1).strip()
    data['birth_date_raw'] = m.group(2).strip()
# spouse: "married Rebecca Rippy in the Salt Lake Temple on 7 June 1967"
m = re.search(r'married ([^,]+) in the [^,]+,? on (\d{1,2} \w+ \d{4})', text, re.I)
if m:
    data['spouse_name'] = m.group(1).strip()
    data['spouse_year'] = m.group(2).strip().split()[-1]
# children: "They have [NUMBER] children"
words = {'one':'1','two':'2','three':'3','four':'4','five':'5','six':'6','seven':'7','eight':'8','nine':'9','ten':'10'}
m = re.search(r'They have (\d+|' + '|'.join(words.keys()) + r') children', text, re.I)
if m:
    val = m.group(1).lower()
    data['children_count'] = words.get(val, val)
for k, v in data.items():
    sys.stdout.write(f'{k}={v}\n')
PYEOF

    "$py" "$py_script" "$win_txt" 2>/dev/null || true
    rm -f "$py_script" 2>/dev/null
}

# ─── fuentes secundarias: wikidata.json ────────────────────────────────────

parse_wikidata() {
    local json="$1"
    [[ -f "$json" ]] || return

    python -c "
import json, sys, urllib.parse
with open('$json', 'r', encoding='utf-8') as f:
    data = json.load(f)
if data.get('birthDate'):
    sys.stdout.write(f'birth_date={data[\"birthDate\"]}\n')
if data.get('deathDate'):
    sys.stdout.write(f'death_date={data[\"deathDate\"]}\n')
if data.get('image'):
    url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' + urllib.parse.quote(data['image'])
    sys.stdout.write(f'image_url={url}\n')
if data.get('description') and not data.get('description', '').startswith('American'):
    sys.stdout.write(f'description_fallback={data[\"description\"]}\n')
" 2>/dev/null || true
}

# ─── fuentes secundarias: authors-enriched.json ─────────────────────────────

parse_authors_enriched() {
    local name="$1"
    local json="$CORPUS_DIR/authors-enriched.json"
    [[ -f "$json" ]] || return

    python -c "
import json, sys
with open('$json', 'r', encoding='utf-8') as f:
    data = json.load(f)
name_lower = '$name'.lower().strip()
for entry in data:
    ename = entry.get('name', '').lower().strip()
    if ename == name_lower or ename.startswith(name_lower):
        desc = entry.get('description') or ''
        if desc:
            sys.stdout.write(f'description={desc}\n')
        callings = entry.get('callings') or []
        if callings:
            import json as j
            sys.stdout.write(f'callings_json={j.dumps(callings, ensure_ascii=False)}\n')
        return
" 2>/dev/null || true
}

# ─── traducir llamamiento EN → ES ────────────────────────────────────────────

translate_calling() {
    local en="$1"
    # normalizar y mapear
    en=$(echo "$en" | tr '[:upper:]' '[:lower:]')
    case "$en" in
        *"president of the church"*)            echo "Presidente de la Iglesia";;
        *"counselor in the first presidency"*)  echo "Consejero en la Primera Presidencia";;
        *"apostle"*)                            echo "Apóstol";;
        *"general authority seventy"*)          echo "Setenta Autoridad General";;
        *"area seventy"*)                       echo "Setenta de Área";;
        *"assistant to the quorum of the twelve"*) echo "Asistente al Cuórum de los Doce";;
        *"presiding bishop"*)                   echo "Obispo Presidente";;
        *"presiding bishopric"*)                echo "Obispado Presidente";;
        *"general patriarch"*)                  echo "Patriarca General";;
        *"relief society"*)                     echo "Presidencia General de la Sociedad de Socorro";;
        *"sunday school"*)                      echo "Presidencia General de la Escuela Dominical";;
        *"young men"*)                          echo "Presidencia General de los Hombres Jóvenes";;
        *"young women"*)                        echo "Presidencia General de las Mujeres Jóvenes";;
        *"primary"*)                            echo "Presidencia General de la Primaria";;
        *"mission president"*|"mission president"*) echo "Presidente de Misión";;
        *"stake president"*)                    echo "Presidente de Estaca";;
        *"high councilor"*)                     echo "Sumo Consejero";;
        *"bishop"*)                             echo "Obispo";;
        *)
            # capitalizar primera letra como fallback
            echo "$en" | sed 's/.*/\u&/'
            ;;
    esac
}

# ─── actualizar meta en WordPress ─────────────────────────────────────────────

update_meta() {
    local post_id="$1" key="$2" value="$3"
    if [[ -z "$value" ]]; then
        return
    fi
    # strip carriage return from Windows Python output
    value=$(echo "$value" | tr -d '\r')
    docker exec "$CLI_CONTAINER" wp post meta update "$post_id" "$key" "$value" >/dev/null 2>&1 || true
    ok "$key → $value"
}

set_complex_field() {
    local post_id="$1" field="$2"
    shift 2
    # construye PHP que llama carbon_set_post_meta
    local php
    php=$(cat <<PHP
\$data = [];
$@
if (function_exists('carbon_set_post_meta')) {
    carbon_set_post_meta($post_id, '$field', \$data);
    echo "OK";
}
PHP
)
    docker exec "$CLI_CONTAINER" wp eval "$php" 2>/dev/null
}

# ─── descargar y asignar imagen destacada ────────────────────────────────────

set_featured_image() {
    local post_id="$1" image_url="$2" title="$3"
    [[ -z "$image_url" ]] && return

    info "Descargando imagen destacada …"
    # descargar dentro del contenedor cli (MSYS_NO_PATHCONV evita traducción de rutas)
    local tmpfile="/tmp/bc-img-$$.jpg"
    # URL-encode espacios
    image_url=$(echo "$image_url" | sed 's/ /%20/g')

    MSYS_NO_PATHCONV=1 docker exec "$CLI_CONTAINER" sh -c "curl -sL -o '$tmpfile' '$image_url'" 2>/dev/null || {
        info "No se pudo descargar: $image_url"
        return
    }

    local result
    result=$(MSYS_NO_PATHCONV=1 docker exec "$CLI_CONTAINER" wp media import "$tmpfile" \
        --post_id="$post_id" --featured_image --title="$title" 2>&1) || true
    MSYS_NO_PATHCONV=1 docker exec "$CLI_CONTAINER" rm -f "$tmpfile" 2>/dev/null

    if echo "$result" | rg -q "Imported"; then
        ok "Imagen destacada asignada: $image_url"
    else
        info "No se pudo asignar imagen destacada: $(echo "$result" | head -1)"
    fi
}

# ─── MAIN ─────────────────────────────────────────────────────────────────────

main() {
    local input="${1:-}"
    [[ -n "$input" ]] || die "Uso: $0 <slug|ID|nombre>"

    info "Resolviendo post_id para: $input"
    local post_id
    post_id=$(resolve_post_id "$input")
    ok "Post ID: $post_id"

    local slug
    slug=$(get_slug "$post_id")
    ok "Slug: $slug"

    info "Buscando carpeta en corpus para: $slug"
    local person_dir
    person_dir=$(find_corpus_dir "$slug")
    person_dir=$(norm_corpus_path "$person_dir")
    ok "Corpus: $person_dir"

    # ── archivos fuente ──
    local ldsorg="$person_dir/ldsorg.html"
    local wikipedia="$person_dir/wikipedia.html"
    local title
    title=$(docker exec "$CLI_CONTAINER" wp post get "$post_id" --field=post_title 2>/dev/null)

    # ── parse: cascada de fuentes ──
    declare -A data

    # 1. ldsorg.html (Church website — fuente primaria)
    if [[ -f "$ldsorg" ]]; then
        info "Fuente: ldsorg.html"
        while IFS='=' read -r key value; do
            [[ -n "$key" ]] && data["$key"]="$value"
        done < <(parse_ldsorg "$ldsorg" | tr -d '\r')
    fi

    # 2. church-news/<slug>.html (obituario)
    if [[ -z "${data[birth_date]:-}" || -z "${data[spouse_name]:-}" ]]; then
        info "Fuente: church-news/"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_church_news "$slug" | tr -d '\r')
    fi

    # 3. cn-bio-data.json (año de nacimiento/muerte)
    if [[ -z "${data[birth_date]:-}" ]]; then
        info "Fuente: cn-bio-data.json"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_cn_bio "$title" | tr -d '\r')
    fi

    # 4. wikidata.json (años, imagen, descripción corta)
    local wikidata="$person_dir/wikidata.json"
    if [[ -f "$wikidata" ]]; then
        info "Fuente: wikidata.json"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_wikidata "$wikidata" | tr -d '\r')
    fi

    # 5. chd.html (Church History Biographical Database)
    local chd="$person_dir/chd.html"
    if [[ -f "$chd" ]]; then
        info "Fuente: chd.html"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_chd "$chd")
    fi

    # 6. biographical-encyclopedia (Andrew Jenson's Biographical Encyclopedia, vols 1-4)
    info "Fuente: biographical-encyclopedia/"
    while IFS='=' read -r key value; do
        [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
    done < <(parse_bio_encyclopedia "$title" | tr -d '\r')

    # 7. ensign-bio.txt (Ensign magazine biography)
    local ensign_bio="$person_dir/ensign-bio.txt"
    if [[ -f "$ensign_bio" ]]; then
        info "Fuente: ensign-bio.txt"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_ensign_bio "$ensign_bio")
    fi

    # 8. historical-conf.html (Historical General Conferences)
    local historical_conf="$person_dir/historical-conf.html"
    if [[ -f "$historical_conf" && -z "${data[spouse_name]:-}" ]]; then
        info "Fuente: historical-conf.html"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_historical_conf "$historical_conf")
    fi
    # 9. wikipedia.html (si existe)

    if [[ -f "$wikipedia" ]]; then
        info "Fuente: wikipedia.html"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_wikipedia "$wikipedia")
    fi

    # 10. authors-enriched.json (descripción y llamamientos en español)
    if [[ -z "${data[description]:-}" ]]; then
        info "Fuente: authors-enriched.json"
        while IFS='=' read -r key value; do
            [[ -n "$key" && -z "${data[$key]:-}" ]] && data["$key"]="$value"
        done < <(parse_authors_enriched "$title")
    fi

    # ── procesar fechas ──
    local birth_date="${data[birth_date]:-${data[birth_date_raw]:-}}"
    local birth_place="${data[birth_place]:-}"
    local death_date="${data[death_date]:-${data[death_date_raw]:-}}"
    local death_place="${data[death_place]:-}"
    local father="${data[father]:-}"
    local mother="${data[mother]:-}"
    local mother_maiden="${data[mother_maiden]:-}"
    # traducir países en lugares
    birth_place=$(echo "$birth_place" | sed -E \
        -e 's/, United States$/, Estados Unidos/g' \
        -e 's/, Germany$/, Alemania/g' \
        -e 's/, Brazil$/, Brasil/g' \
        -e 's/, Mexico$/, México/g' \
        -e 's/, Canada$/, Canadá/g' \
        -e 's/, England$/, Inglaterra/g' \
        -e 's/, Australia$/, Australia/g' \
        -e 's/, France$/, Francia/g' \
        -e 's/, Italy$/, Italia/g' \
        -e 's/, Spain$/, España/g' \
        -e 's/, Japan$/, Japón/g')
    death_place=$(echo "$death_place" | sed -E \
        -e 's/, United States$/, Estados Unidos/g' \
        -e 's/, Germany$/, Alemania/g' \
        -e 's/, Brazil$/, Brasil/g' \
        -e 's/, Mexico$/, México/g' \
        -e 's/, Canada$/, Canadá/g' \
        -e 's/, England$/, Inglaterra/g' \
        -e 's/, Australia$/, Australia/g' \
        -e 's/, France$/, Francia/g' \
        -e 's/, Italy$/, Italia/g' \
        -e 's/, Spain$/, España/g' \
        -e 's/, Japan$/, Japón/g')
    # construir spouse_line desde church-news si no vino de ldsorg
    local spouse_line="${data[spouse_line]:-}"
    if [[ -z "$spouse_line" && -n "${data[spouse_name]:-}" ]]; then
        spouse_line="He married ${data[spouse_name]} in ${data[spouse_year]:-}"
    fi
    local description="${data[description]:-${data[description_fallback]:-}}"
    local callings_json="${data[callings_json]:-}"

    # traducir fecha si está en inglés
    if [[ -n "$birth_date" ]]; then
        birth_date=$(translate_date "$birth_date")
    fi
    if [[ -n "$death_date" ]]; then
        death_date=$(translate_date "$death_date")
    fi

    # traducir descripción si está en inglés
    if [[ -n "$description" ]] && ! echo "$description" | rg -qi "[áéíóúñ]"; then
        local translated
        translated=$(translate_calling "$description")
        if [[ -n "$translated" ]]; then
            description="$translated"
        fi
    fi

    # ── actualizar WordPress ──
    info "Actualizando WordPress …"

    update_meta "$post_id" "_author_description" "$description"

    update_meta "$post_id" "_author_birth_date" "$birth_date"
    update_meta "$post_id" "_author_birth_place" "$birth_place"

    update_meta "$post_id" "_author_death_date" "$death_date"
    update_meta "$post_id" "_author_death_place" "$death_place"

    update_meta "$post_id" "_author_father" "$father"
    update_meta "$post_id" "_author_mother" "$mother"
    update_meta "$post_id" "_author_mother_maiden" "$mother_maiden"

    # nationality — intenta inferir de birth_place
    if [[ -n "$birth_place" ]]; then
        local nationality=""
        if echo "$birth_place" | rg -qi "alem|germany"; then
            nationality="Alemana"
        elif echo "$birth_place" | rg -qi "america|estados unidos|united states|utah|vermont"; then
            nationality="Estadounidense"
        elif echo "$birth_place" | rg -qi "inglaterra|londres|england|brit|reino unido|united kingdom|uk "; then
            nationality="Británica"
        elif echo "$birth_place" | rg -qi "méxico|mexico"; then
            nationality="Mexicana"
        elif echo "$birth_place" | rg -qi "brasil|brazil"; then
            nationality="Brasileña"
        elif echo "$birth_place" | rg -qi "canadá|canada"; then
            nationality="Canadiense"
        elif echo "$birth_place" | rg -qi "australia"; then
            nationality="Australiana"
        elif echo "$birth_place" | rg -qi "franc|france"; then
            nationality="Francesa"
        elif echo "$birth_place" | rg -qi "ital"; then
            nationality="Italiana"
        elif echo "$birth_place" | rg -qi "españa|spain"; then
            nationality="Española"
        fi
        if [[ -n "$nationality" ]]; then
            update_meta "$post_id" "_author_nationality" "$nationality"
        fi
    fi

    # spouse — extraer de la línea de boda
    if [[ -n "$spouse_line" ]]; then
        # patrón típico: "married X in YEAR"
        local spouse_name
        spouse_name=$(echo "$spouse_line" | rg -oP '(?<=married )[\w\s]+(?= in)') || true
        spouse_name=$(echo "$spouse_name" | tr -d '\r')
        local marriage_year
        marriage_year=$(echo "$spouse_line" | rg -oP '(?<=in )\d{4}') || true
        marriage_year=$(echo "$marriage_year" | tr -d '\r')
        local children_count
        children_count=$(echo "$spouse_line" | rg -oP '(\d+) children' | rg -oP '\d+') || true
        children_count="${children_count:-${data[children_count]:-}}"
        children_count=$(echo "$children_count" | tr -d '\r')
        if [[ -n "$spouse_name" ]]; then
            local php_spouse
            php_spouse=$(cat <<PHP
\$data[] = [
    "name" => "$spouse_name",
    "marriage_year" => "$marriage_year",
    "end_year" => "",
    "children_count" => "$children_count",
];
PHP
)
            set_complex_field "$post_id" "_author_spouses" "$php_spouse" && ok "Cónyuge guardado: $spouse_name"
        fi
    fi

    # ── is_ga: si description contiene "General Authority" o "Setenta" ──
    if echo "$description" | rg -qi "general authority|setenta"; then
        update_meta "$post_id" "_author_is_ga" "1"
    fi

    # ── witness_type ──
    local witness=""
    if echo "$description" | rg -qi "three witnesses|tres testigos"; then
        witness="three-witnesses"
    elif echo "$description" | rg -qi "eight witnesses|ocho testigos"; then
        witness="eight-witnesses"
    fi
    if [[ -n "$witness" ]]; then
        update_meta "$post_id" "_author_witness_type" "$witness"
    fi

    # ── imagen destacada: og:image (ldsorg) → image_url (cn-bio) ──
    local og_image="${data[og_image]:-${data[image_url]:-}}"
    if [[ -n "$og_image" ]]; then
        # saltar placeholders conocidos (logo genérico de Church News, etc.)
        if echo "$og_image" | rg -qi "fallbackImage|placeholder|logo"; then
            info "Imagen es placeholder, se omite: $og_image"
        else
            set_featured_image "$post_id" "$og_image" "$slug"
        fi
    fi

    # ── actualizar raw meta para compatibilidad hacia atrás ──
    info "Sincronizando metadatos raw (backward compat) …"
    docker exec "$CLI_CONTAINER" wp eval '
$post_id = '$post_id';
if (function_exists("carbon_get_post_meta")) {
    foreach (["_author_spouses", "_author_callings"] as $key) {
        $val = carbon_get_post_meta($post_id, $key);
        if (is_array($val) && !empty($val)) {
            update_post_meta($post_id, $key, wp_json_encode($val));
        }
    }
    // sync taxonomy terms for callings
    $callings = carbon_get_post_meta($post_id, "_author_callings");
    if (is_array($callings) && !empty($callings)) {
        $term_slugs = array_unique(array_column($callings, "calling"));
        wp_set_object_terms($post_id, array_values($term_slugs), "bc_author_calling", true);
    }
}
echo "Sync done.\n";
' 2>/dev/null

    ok "Persona completada: https://localhost:8080/glosario/persona/$slug/"
}

main "$@"
