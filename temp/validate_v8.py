"""v8: English→Spanish location translation via book-specific search.

Strategy:
1. Search English for name → get testament + book_en_slug + chapter
2. Map book_en_slug to Spanish folder name (reverse of BOOK_ES_FOLDER)
3. Search Spanish candidates in es/scriptures/{testament}/{es_folder}/
4. Verify same chapter match within results
5. Fall back to global search (limit=30) if book-specific fails
"""

import json, sys, urllib.request, re, time

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

BOOK_FOLD = {
    "1 samuel": "1-samuel",
    "2 samuel": "2-samuel",
    "1 kings": "1-kings",
    "2 kings": "2-kings",
    "1 chronicles": "1-cronicas",
    "2 chronicles": "2-cronicas",
    "1 corinthians": "1-corintios",
    "2 corinthians": "2-corintios",
    "1 thessalonians": "1-tesalonicenses",
    "2 thessalonians": "2-tesalonicenses",
    "1 timothy": "1-timoteo",
    "2 timothy": "2-timoteo",
    "1 peter": "1-pedro",
    "2 peter": "2-pedro",
    "1 john": "1-juan",
    "2 john": "2-juan",
    "3 john": "3-juan",
    "song of solomon": "cantares",
    "song of sol.": "cantares",
    "genesis": "genesis",
    "exodus": "exodo",
    "exod": "exodo",
    "leviticus": "levitico",
    "numbers": "numeros",
    "deuteronomy": "deuteronomio",
    "joshua": "josue",
    "judges": "jueces",
    "judg": "jueces",
    "ruth": "rut",
    "ezra": "esdras",
    "nehemiah": "nehemias",
    "neh": "nehemias",
    "esther": "ester",
    "job": "job",
    "psalms": "salmos",
    "ps": "salmos",
    "proverbs": "proverbios",
    "prov": "proverbios",
    "ecclesiastes": "eclesiastes",
    "isaiah": "isaias",
    "jeremiah": "jeremias",
    "lamentations": "lamentaciones",
    "ezekiel": "ezequiel",
    "daniel": "daniel",
    "hosea": "oseas",
    "joel": "joel",
    "amos": "amos",
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
    "john": "juan",
    "acts": "hechos",
    "romans": "romanos",
    "galatians": "galatas",
    "ephesians": "efesios",
    "philippians": "filipenses",
    "colossians": "colosenses",
    "titus": "tito",
    "philemon": "filemon",
    "hebrews": "hebreos",
    "james": "santiago",
    "jude": "judas",
    "revelation": "apocalipsis",
    "rev": "apocalipsis",
    "josue": "josue",
    "jueces": "jueces",
    "hechos": "hechos",
    "mateo": "mateo",
    "marcos": "marcos",
    "lucas": "lucas",
    "juan": "juan",
    "romanos": "romanos",
    "1-corintios": "1-corintios",
    "2-corintios": "2-corintios",
}

BOOK_ES_FOLDER = {
    "jueces": "judges",
    "josue": "joshua",
    "exodo": "exodus",
    "levitico": "leviticus",
    "numeros": "numbers",
    "deuteronomio": "deuteronomy",
    "1-reyes": "1-kings",
    "2-reyes": "2-kings",
    "1-cronicas": "1-chronicles",
    "2-cronicas": "2-chronicles",
    "esdras": "ezra",
    "nehemias": "nehemiah",
    "ester": "ester",
    "salmos": "psalms",
    "proverbios": "proverbs",
    "eclesiastes": "ecclesiastes",
    "cantares": "song-of-solomon",
    "isaias": "isaiah",
    "jeremias": "jeremiah",
    "lamentaciones": "lamentations",
    "ezequiel": "ezekiel",
    "oseas": "hosea",
    "abdias": "obadiah",
    "jonas": "jonah",
    "miqueas": "micah",
    "sofonias": "zephaniah",
    "hageo": "haggai",
    "zacarias": "zechariah",
    "malaquias": "malachi",
    "mateo": "matthew",
    "marcos": "mark",
    "lucas": "luke",
    "hechos": "acts",
    "romanos": "romans",
    "1-corintios": "1-corinthians",
    "2-corintios": "2-corinthians",
    "galatas": "galatians",
    "efesios": "ephesians",
    "filipenses": "philippians",
    "colosenses": "colossians",
    "1-tesalonicenses": "1-thessalonians",
    "2-tesalonicenses": "2-thessalonians",
    "1-timoteo": "1-timothy",
    "2-timoteo": "2-timothy",
    "filemon": "philemon",
    "hebreos": "hebrews",
    "santiago": "james",
    "1-pedro": "1-peter",
    "2-pedro": "2-peter",
    "judas": "jude",
    "apocalipsis": "revelation",
}

EN_FOLDER_TO_ES = {v: k for k, v in BOOK_ES_FOLDER.items()}


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


def gen_candidates(name):
    base = name.rstrip(" 0123456789")
    cands = {base}
    if base.endswith("h"):
        bh = base[:-1]
        cands.add(bh)
        if bh and bh[-1] in "aeio":
            cands.add(bh[:-1] + {"a": "á", "e": "é", "i": "í", "o": "ó"}[bh[-1]])
    else:
        for v, a in [("a", "á"), ("e", "é"), ("i", "í"), ("o", "ó")]:
            if base.endswith(v):
                cands.add(base[:-1] + a)
    if "ph" in base:
        cands.add(base.replace("ph", "f"))
    if "th" in base:
        cands.add(base.replace("th", "t"))
    if "sh" in base.lower():
        cands.add(base.lower().replace("sh", "s").capitalize())
    if base.endswith("y"):
        cands.add(base[:-1] + "i")
        cands.add(base[:-1] + "í")
    if len(base) > 2 and base[-1] == base[-2]:
        cands.add(base[:-1])
    return sorted([c for c in cands if len(c) >= 3], key=len, reverse=True)


def fold_ref(ref):
    if not ref:
        return None
    ref = ref.strip().replace("\u2013", "-").replace("\u2014", "-")
    m = re.match(
        r"(\d\s*)?([A-Za-záéíóúñÁÉÍÓÚÑ]+[a-záéíóúñA-ZÁÉÍÓÚÑ.]*(?:\s+[A-Za-z][a-záéíóúñ]+)*)\s+(\d+):",
        ref,
    )
    if not m:
        return None
    prefix = (m.group(1) or "").strip()
    book_raw = m.group(2).strip()
    chapter = m.group(3)
    full_name = (prefix + " " + book_raw).strip().lower()
    if full_name in BOOK_FOLD:
        return (BOOK_FOLD[full_name], chapter)
    if book_raw.lower() in BOOK_FOLD:
        return (BOOK_FOLD[book_raw.lower()], chapter)
    if book_raw.endswith("."):
        if book_raw[:-1].lower() in BOOK_FOLD:
            return (BOOK_FOLD[book_raw[:-1].lower()], chapter)
    return None


def text_contains_form(text, cand):
    for form in [cand, cand[0].upper() + cand[1:], cand.lower(), cand.upper()]:
        if len(form) < 3:
            continue
        pat = (
            r"(?<![a-zA-ZáéíóúñÁÉÍÓÚÑ])" + re.escape(form) + r"(?![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
        )
        m = re.search(pat, text)
        if m:
            return m.group()
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

    # Search English to get book + chapter
    r = mc("search_text", {"query": base, "limit": 1, "source_filter": "en/scriptures"})
    if not r or not r.get("results"):
        result["note"] = "no_en_passage"
        return result
    hit = r["results"][0]
    ref_en = hit.get("reference", "")
    fpath_en = hit.get("file_path", "")

    m = re.search(r"en/scriptures/([^/]+)/([^/]+)/(\d+)\.txt", fpath_en)
    if not m:
        result["note"] = f"no_fpath|{fpath_en}"
        return result
    testament = m.group(1)
    book_en_slug = m.group(2)
    ch_en = m.group(3)
    result["en_ref"] = ref_en

    # Map English book slug to Spanish folder name
    es_book_folder = EN_FOLDER_TO_ES.get(book_en_slug, book_en_slug)

    cands = gen_candidates(name)
    result["cands"] = cands
    found_best = None
    found_ref = None

    # Phase 1: Book-specific search
    for cand in cands:
        source_filter = f"es/scriptures/{testament}/{es_book_folder}"
        rs = mc(
            "search_text", {"query": cand, "limit": 5, "source_filter": source_filter}
        )
        time.sleep(0.1)
        if not rs or not rs.get("results"):
            continue
        for hit2 in rs["results"]:
            ref_es = hit2.get("reference", "")
            text = hit2["text"]
            folded = fold_ref(ref_es)
            if not folded:
                continue
            es_book, es_ch = folded
            if es_ch == ch_en:
                match = text_contains_form(text, cand)
                if match:
                    found_best = match
                    found_ref = ref_es
                    break
        if found_best:
            break

    if found_best:
        result["action"] = "update"
        result["spanish"] = found_best + suffix
        result["note"] = f"book|{found_ref}"
        return result

    # Phase 2: Global fallback with limit=30
    for cand in cands:
        rs = mc(
            "search_text",
            {"query": cand, "limit": 30, "source_filter": "es/scriptures"},
        )
        time.sleep(0.1)
        if not rs or not rs.get("results"):
            continue
        for hit2 in rs["results"]:
            ref_es = hit2.get("reference", "")
            text = hit2["text"]
            folded = fold_ref(ref_es)
            if not folded:
                continue
            es_book, es_ch = folded
            if es_ch == ch_en:
                match = text_contains_form(text, cand)
                if match:
                    found_best = match
                    found_ref = ref_es
                    break
        if found_best:
            break

    if found_best:
        result["action"] = "update"
        result["spanish"] = found_best + suffix
        result["note"] = f"global|{found_ref}"
    else:
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
        "book_found": 0,
        "global_found": 0,
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
                if r.get("note", "").startswith("hard_map"):
                    stats["hard_map"] += 1
                elif r.get("note", "").startswith("book"):
                    stats["book_found"] += 1
                elif r.get("note", "").startswith("global"):
                    stats["global_found"] += 1
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
                f"B:{stats['book_found']} G:{stats['global_found']} "
                f"HM:{stats['hard_map']} NF:{stats['not_found']} E:{stats['errors']} ---\n"
            )

    print(
        json.dumps(
            {"stats": stats, "results": results_out}, ensure_ascii=False, indent=2
        )
    )


if __name__ == "__main__":
    main()
