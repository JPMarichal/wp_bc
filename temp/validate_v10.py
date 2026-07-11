"""v10: English→Spanish via direct passage cross-reference (instrucciones JP).

Estrategia (la correcta desde el principio):
1. Buscar nombre en inglés → obtener referencia + libro + capítulo
2. Leer el MISMO capítulo en español vía búsqueda FTS
3. Extraer del texto español la palabra que equivale al nombre inglés
   (sin reglas fonéticas, sin candidatos — solo normalizar acentos y comparar)
"""

import json, sys, urllib.request, re, time, unicodedata

HARD_MAP = {
    "Gibeah": "Gabaa",
    "Gibeath": "Gabaa",
    "Gomorrah": "Gomorra",
    "Cyprus": "Chipre",
    "Nazareth": "Nazaret",
    "Bethlehem": "Belén",
    "Nineveh": "Nínive",
    "Capernaum": "Capernaúm",
    "Calvary": "Calvario",
    "Kidron": "Cedrón",
    "Smyrna": "Esmirna",
    "Sparta": "Esparta",
    "Jericho": "Jericó",
    "Tirzah": "Tirsa",
    "Bashan": "Basán",
    "Cana": "Caná",
    "Succoth": "Sucot",
    "Penuel": "Peniel",
    "Sharon": "Sarón",
    "Shur": "Sur",
    "Ashdod": "Asdod",
    "Ashkelon": "Ascalón",
    "Ekron": "Ecrón",
    "Gath": "Gat",
    "Joppa": "Jope",
    "Tyre": "Tiro",
    "Sidon": "Sidón",
    "Euphrates": "Éufrates",
    "Tigris": "Tigris",
    "Pishon": "Pisón",
    "Gihon": "Gihón",
    "Havilah": "Havila",
    "Zorah": "Zora",
    "Sinai": "Sinaí",
    "Lycia": "Licia",
    "Lydia": "Lidia",
    "Mysia": "Misia",
    "Illyricum": "Ilírico",
    "Dalmatia": "Dalmacia",
    "Ephesus": "Éfeso",
    "Pergamum": "Pérgamo",
    "Thessalonica": "Tesalónica",
    "Philippi": "Filipos",
    "Cenchrea": "Cencrea",
    "Troas": "Tróade",
    "Assos": "Asós",
    "Mitylene": "Mitilene",
    "Samos": "Samos",
    "Trogyllium": "Trogilio",
    "Neapolis": "Neápolis",
    "Berea": "Berea",
    "Athens": "Atenas",
    "Corinth": "Corinto",
    "Salamis": "Salamina",
    "Paphos": "Pafos",
    "Perga": "Perga",
    "Antioch": "Antioquía",
    "Iconium": "Iconio",
    "Lystra": "Listra",
    "Derbe": "Derbe",
    "Attalia": "Atalía",
    "Miletus": "Mileto",
    "Patmos": "Patmos",
    "Chios": "Quíos",
    "Rhodes": "Rodas",
    "Puteoli": "Puteoli",
    "Rhegium": "Regio",
    "Syracuse": "Siracusa",
    "Fair Havens": "Puertos Hermosos",
    "Adramyttium": "Adramitio",
    "Myra": "Mira",
    "Cnidus": "Cnido",
    "Phoenix": "Fénix",
    "Alexandria": "Alejandría",
    "Cyrene": "Cirene",
    "Tarsus": "Tarso",
    "Seleucia": "Seleucia",
    "Ptolemais": "Tolemaida",
    "Caesarea": "Cesarea",
    "Jerusalem": "Jerusalén",
    "Samaria": "Samaria",
    "Judea": "Judea",
    "Galilee": "Galilea",
    "Decapolis": "Decápolis",
    "Perea": "Perea",
    "Arabia": "Arabia",
    "Egypt": "Egipto",
    "Mesopotamia": "Mesopotamia",
    "Babylon": "Babilonia",
    "Persia": "Persia",
    "Media": "Media",
    "Assyria": "Asiria",
    "Elam": "Elam",
    "Chaldea": "Caldea",
    "Moab": "Moab",
    "Ammon": "Amón",
    "Edom": "Edom",
    "Canaan": "Canaán",
    "Philistia": "Filistea",
    "Golgotha": "Gólgota",
    "Gethsemane": "Getsemaní",
    "Arimathea": "Arimatea",
    "Bethany": "Betania",
    "Bethphage": "Betfagé",
    "Emmaus": "Emaús",
    "Nain": "Naín",
    "Chorazin": "Corazín",
    "Bethsaida": "Betsaida",
    "Tiberias": "Tiberias",
    "Magdala": "Magdala",
}

EN_FOLDER_TO_ES = {
    "judges": "jueces",
    "joshua": "josue",
    "exodus": "exodo",
    "leviticus": "levitico",
    "numbers": "numeros",
    "deuteronomy": "deuteronomio",
    "1-kings": "1-reyes",
    "2-kings": "2-reyes",
    "1-chronicles": "1-cronicas",
    "2-chronicles": "2-cronicas",
    "ezra": "esdras",
    "nehemiah": "nehemias",
    "esther": "ester",
    "psalms": "salmos",
    "proverbs": "proverbios",
    "ecclesiastes": "eclesiastes",
    "song-of-solomon": "cantares",
    "isaiah": "isaias",
    "jeremiah": "jeremias",
    "lamentations": "lamentaciones",
    "ezekiel": "ezequiel",
    "hosea": "oseas",
    "obadiah": "abdias",
    "jonah": "jonas",
    "micah": "miqueas",
    "nahum": "nahum",
    "habakkuk": "habacuc",
    "zephaniah": "sofonias",
    "haggai": "hageo",
    "zechariah": "zacarias",
    "malachi": "malaquias",
    "matthew": "mateo",
    "mark": "marcos",
    "luke": "lucas",
    "acts": "hechos",
    "romans": "romanos",
    "1-corinthians": "1-corintios",
    "2-corinthians": "2-corintios",
    "galatians": "galatas",
    "ephesians": "efesios",
    "philippians": "filipenses",
    "colossians": "colosenses",
    "1-thessalonians": "1-tesalonicenses",
    "2-thessalonians": "2-tesalonicenses",
    "1-timothy": "1-timoteo",
    "2-timothy": "2-timoteo",
    "philemon": "filemon",
    "hebrews": "hebreos",
    "james": "santiago",
    "1-peter": "1-pedro",
    "2-peter": "2-pedro",
    "jude": "judas",
    "revelation": "apocalipsis",
}


def normalize(s):
    return unicodedata.normalize("NFKD", s).encode("ASCII", "ignore").decode().lower()


def mc(method, params):
    req = urllib.request.Request(
        "http://localhost:4300/mcp/",
        data=json.dumps(
            {
                "jsonrpc": "2.0",
                "id": 1,
                "method": "tools/call",
                "params": {"name": method, "arguments": params},
            }
        ).encode(),
        headers={
            "Content-Type": "application/json",
            "Accept": "application/json, text/event-stream",
        },
    )
    body = urllib.request.urlopen(req, timeout=10).read().decode()
    for line in body.split(chr(10)):
        if line.startswith("data: "):
            d = json.loads(line[6:])
            return json.loads(d["result"]["content"][0]["text"])
    return None


def extract_spanish_name(text, en_name):
    """Busca en el texto español la palabra que corresponde al nombre inglés.
    Normaliza acentos y mayúsculas para la comparación."""
    en_norm = normalize(en_name)
    words = re.findall(r"[A-Za-záéíóúñÁÉÍÓÚÑ]+(?:[-'][A-Za-záéíóúñÁÉÍÓÚÑ]+)*", text)
    for w in words:
        if normalize(w) == en_norm:
            return w
    return None


def process(loc):
    name = loc["post_title"].strip()
    lid = loc["ID"]
    base = name.rstrip(" 0123456789")
    suffix = name[len(base) :]
    result = {"id": lid, "title": name, "action": "review", "spanish": None}

    if base in HARD_MAP:
        result["action"] = "update"
        result["spanish"] = HARD_MAP[base] + suffix
        result["note"] = "hard_map"
        return result

    # 1. Buscar en inglés → obtener referencia + libro + capítulo
    r = mc("search_text", {"query": base, "limit": 5, "source_filter": "en/scriptures"})
    if not r or not r.get("results"):
        result["note"] = "no_en_passage"
        return result

    # Verificar que el nombre aparece en el texto inglés
    hit = None
    for h in r["results"]:
        if extract_spanish_name(h["text"], base):
            hit = h
            break
    if not hit:
        result["note"] = "no_en_passage"
        return result

    ref_en = hit.get("reference", "")
    fpath_en = hit.get("file_path", "")
    result["en_ref"] = ref_en

    m = re.search(r"en/scriptures/([^/]+)/([^/]+)/(\d+)\.txt", fpath_en)
    if not m:
        result["note"] = f"no_fpath|{fpath_en}"
        return result
    testament = m.group(1)
    book_en_slug = m.group(2)
    ch_en = m.group(3)

    # 2. Mapear a carpeta española
    es_book_folder = EN_FOLDER_TO_ES.get(book_en_slug, book_en_slug)

    # 3. Leer el MISMO capítulo en español
    source = f"es/scriptures/{testament}/{es_book_folder}/{ch_en}"
    rs = mc("search_text", {"query": base, "limit": 3, "source_filter": source})
    time.sleep(0.1)

    if rs and rs.get("results"):
        for hit2 in rs["results"]:
            ref_es = hit2.get("reference", "")
            text = hit2["text"]
            # 4. Extraer la ortografía exacta del RV60
            span = extract_spanish_name(text, base)
            if span:
                result["action"] = "update"
                result["spanish"] = span + suffix
                result["note"] = f"xref|{ref_es}"
                return result

    # Fallback: buscar en todo el libro si no se encuentra en el capítulo exacto
    source2 = f"es/scriptures/{testament}/{es_book_folder}"
    rs2 = mc("search_text", {"query": base, "limit": 5, "source_filter": source2})
    time.sleep(0.1)
    if rs2 and rs2.get("results"):
        for hit2 in rs2["results"]:
            ref_es = hit2.get("reference", "")
            text = hit2["text"]
            span = extract_spanish_name(text, base)
            if span:
                result["action"] = "update"
                result["spanish"] = span + suffix
                result["note"] = f"xref_book|{ref_es}"
                return result

    result["note"] = f"no_es_match|{ref_en}"
    return result


def main():
    locations = json.loads(sys.stdin.read())
    stats = {
        "total": 0,
        "found": 0,
        "not_found": 0,
        "hard_map": 0,
        "errors": 0,
        "xref": 0,
        "xref_book": 0,
    }
    results_out = []

    to_process = [
        l for l in locations if not any(ord(c) > 191 for c in l["post_title"])
    ]
    sys.stderr.write(f"Processing {len(to_process)} names...\n")

    for idx, loc in enumerate(to_process):
        name = loc["post_title"]
        stats["total"] += 1
        sys.stderr.write(f"[{stats['total']}/{len(to_process)}] {name}... ")
        sys.stderr.flush()

        try:
            r = process(loc)
            results_out.append(r)
            if r["action"] == "update":
                stats["found"] += 1
                note = r.get("note", "")
                if note.startswith("hard_map"):
                    stats["hard_map"] += 1
                elif note.startswith("xref|"):
                    stats["xref"] += 1
                elif note.startswith("xref_book"):
                    stats["xref_book"] += 1
                sys.stderr.write(f"→ {r['spanish']} ({r['note']})\n")
            else:
                stats["not_found"] += 1
                sys.stderr.write(f"→ {r['note']}\n")
        except Exception as e:
            stats["errors"] += 1
            import traceback

            sys.stderr.write(f"→ ERROR: {e}\n{traceback.format_exc()}\n")
            results_out.append(
                {"id": loc["ID"], "title": name, "action": "review", "note": str(e)}
            )

        if stats["total"] % 20 == 0:
            pct = int(stats["found"] / max(stats["total"], 1) * 100)
            sys.stderr.write(
                f"--- [{stats['total']}/{len(to_process)}] "
                f"F:{stats['found']}({pct}%) "
                f"X:{stats['xref']} XB:{stats['xref_book']} "
                f"HM:{stats['hard_map']} NF:{stats['not_found']} E:{stats['errors']} ---\n"
            )

    print(
        json.dumps(
            {"stats": stats, "results": results_out}, ensure_ascii=False, indent=2
        )
    )


if __name__ == "__main__":
    main()
