"""v5: Cross-reference English→Spanish using aligned chapter files.

Strategy (from user):
1. Search English → get passage reference + file_path + chapter
2. Construct Spanish file_path using BOOK_MAP
3. Search Spanish in the same chapter file
4. Extract the exact Spanish form from the text
"""

import json, sys, urllib.request, re, time

BOOK_MAP = {
    "genesis": "genesis",
    "exodus": "exodo",
    "leviticus": "levitico",
    "numbers": "numeros",
    "deuteronomy": "deuteronomio",
    "joshua": "josue",
    "judges": "jueces",
    "ruth": "rut",
    "1-samuel": "1-samuel",
    "2-samuel": "2-samuel",
    "1-kings": "1-reyes",
    "2-kings": "2-reyes",
    "1-chronicles": "1-cronicas",
    "2-chronicles": "2-cronicas",
    "ezra": "esdras",
    "nehemiah": "nehemias",
    "esther": "ester",
    "job": "job",
    "psalms": "salmos",
    "proverbs": "proverbios",
    "ecclesiastes": "eclesiastes",
    "song-of-solomon": "cantares",
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
    "titus": "tito",
    "philemon": "filemon",
    "hebrews": "hebreos",
    "james": "santiago",
    "1-peter": "1-pedro",
    "2-peter": "2-pedro",
    "1-john": "1-juan",
    "2-john": "2-juan",
    "3-john": "3-juan",
    "jude": "judas",
    "revelation": "apocalipsis",
    "1-nephi": "1-nefi",
    "2-nephi": "2-nefi",
    "words-of-mormon": "palabras-de-mormon",
    "3-nephi": "3-nefi",
    "4-nephi": "4-nefi",
    "ether": "eter",
    "doctrine-and-covenants": "doctrina-y-convenios",
}

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
}


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


def process(loc):
    name = loc["post_title"].strip()
    lid = loc["ID"]
    base = name.rstrip(" 0123456789")
    suffix = name[len(base) :]
    result = {"id": lid, "title": name, "action": "review", "spanish": None}

    # 1. HARD_MAP lookup
    if base in HARD_MAP:
        result["action"] = "update"
        result["spanish"] = HARD_MAP[base] + suffix
        result["note"] = "hard_map"
        return result

    # 2. Search English to get passage
    r = mc("search_text", {"query": base, "limit": 1, "source_filter": "en/scriptures"})
    if not r or not r.get("results"):
        result["note"] = "no_en_passage"
        return result

    hit = r["results"][0]
    ref = hit.get("reference", "")
    fpath = hit.get("file_path", "")
    text_en = hit["text"]

    # Extract book folder and chapter from English file_path
    m = re.search(r"en/scriptures/([^/]+)/([^/]+)/(\d+)\.txt", fpath)
    if not m:
        result["note"] = f"parse_fail|{fpath}"
        return result

    testament = m.group(1)
    book_en = m.group(2)
    chapter = m.group(3)
    book_es = BOOK_MAP.get(book_en, book_en)

    # 3. Search Spanish in the same chapter file
    es_fpath = f"es/scriptures/{testament}/{book_es}/{chapter}.txt"

    # Generate candidate forms
    cands = [base]
    if base.endswith("h"):
        cands.append(base[:-1])
        if base[:-1][-1] == "a":
            cands.append(base[:-1][:-1] + "á")
        elif base[:-1][-1] == "e":
            cands.append(base[:-1][:-1] + "é")
        elif base[:-1][-1] == "i":
            cands.append(base[:-1][:-1] + "í")
        elif base[:-1][-1] == "o":
            cands.append(base[:-1][:-1] + "ó")
    else:
        if base[-1] == "a":
            cands.append(base[:-1] + "á")
        elif base[-1] == "e":
            cands.append(base[:-1] + "é")
        elif base[-1] == "i":
            cands.append(base[:-1] + "í")
        elif base[-1] == "o":
            cands.append(base[:-1] + "ó")
    if "ph" in base:
        cands.append(base.replace("ph", "f"))
    if "th" in base:
        cands.append(base.replace("th", "t"))
    if "sh" in base.lower():
        cands.append(base.lower().replace("sh", "s").capitalize())

    seen = set()
    cands_unique = []
    for c in cands:
        if c not in seen and len(c) >= 3:
            seen.add(c)
            cands_unique.append(c)

    # Try each candidate in the chapter file
    best = None
    for cand in cands_unique:
        rs = mc("search_text", {"query": cand, "limit": 3, "source_filter": es_fpath})
        time.sleep(0.1)
        if not rs or not rs.get("results"):
            continue
        for hit2 in rs["results"]:
            text = hit2["text"]
            ref_es = hit2.get("reference", "")
            for form in set([cand, cand[0].upper() + cand[1:], cand.lower()]):
                pat = (
                    r"(?<![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                    + re.escape(form)
                    + r"(?![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                )
                m2 = re.search(pat, text)
                if m2:
                    best = m2.group()
                    result["action"] = "update"
                    result["spanish"] = best + suffix
                    result["note"] = f"same_chapter|{es_fpath}|{ref_es}"
                    return result

    # 4. Fallback: search in the whole book
    es_book = f"es/scriptures/{testament}/{book_es}"
    for cand in cands_unique:
        rs = mc("search_text", {"query": cand, "limit": 5, "source_filter": es_book})
        time.sleep(0.1)
        if not rs or not rs.get("results"):
            continue
        for hit2 in rs["results"]:
            text = hit2["text"]
            ref_es = hit2.get("reference", "")
            for form in set([cand, cand[0].upper() + cand[1:], cand.lower()]):
                pat = (
                    r"(?<![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                    + re.escape(form)
                    + r"(?![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                )
                m2 = re.search(pat, text)
                if m2:
                    best = m2.group()
                    result["action"] = "update"
                    result["spanish"] = best + suffix
                    result["note"] = f"same_book|{es_book}|{ref_es}"
                    return result

    # 5. Last resort: scan all Spanish scriptures
    for cand in cands_unique:
        rs = mc(
            "search_text", {"query": cand, "limit": 5, "source_filter": "es/scriptures"}
        )
        time.sleep(0.1)
        if not rs or not rs.get("results"):
            continue
        for hit2 in rs["results"]:
            text = hit2["text"]
            ref_es = hit2.get("reference", "")
            # Find capitalized proper noun
            for word in re.findall(r"\b[A-ZÁÉÍÓÚ][a-záéíóúA-ZÁÉÍÓÚ]*\b", text):
                w_clean = (
                    word.lower()
                    .replace("á", "a")
                    .replace("é", "e")
                    .replace("í", "i")
                    .replace("ó", "o")
                    .replace("ú", "u")
                )
                c_clean = (
                    cand.lower()
                    .replace("á", "a")
                    .replace("é", "e")
                    .replace("í", "i")
                    .replace("ó", "o")
                    .replace("ú", "u")
                )
                if w_clean == c_clean:
                    best = word
                    result["action"] = "update"
                    result["spanish"] = best + suffix
                    result["note"] = f"all_es|{ref_es}"
                    return result
            # Also try the original form directly
            for form in set([cand, cand[0].upper() + cand[1:], cand.lower()]):
                pat = (
                    r"(?<![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                    + re.escape(form)
                    + r"(?![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                )
                m2 = re.search(pat, text)
                if m2:
                    best = m2.group()
                    result["action"] = "update"
                    result["spanish"] = best + suffix
                    result["note"] = f"all_es_exact|{ref_es}"
                    return result

    result["note"] = f"no_es_match|{ref}"
    return result


def main():
    locations = json.loads(sys.stdin.read())
    stats = {"total": 0, "found": 0, "not_found": 0, "hard_map": 0, "errors": 0}
    results = []

    # Only process review items (no accents)
    to_process = [
        l for l in locations if not any(ord(c) > 191 for c in l["post_title"])
    ]

    sys.stderr.write(f"Processing {len(to_process)} names...\n")

    for idx, loc in enumerate(to_process):
        name = loc["post_title"]
        lid = loc["ID"]
        stats["total"] += 1
        sys.stderr.write(f"[{stats['total']}/{len(to_process)}] {name}... ")
        sys.stderr.flush()

        try:
            r = process(loc)
            results.append(r)
            if r["action"] == "update":
                stats["found"] += 1
                if r.get("note", "").startswith("hard_map"):
                    stats["hard_map"] += 1
                sys.stderr.write(f"→ {r['spanish']}\n")
            else:
                stats["not_found"] += 1
                sys.stderr.write(f"→ {r['note']}\n")
        except Exception as e:
            stats["errors"] += 1
            sys.stderr.write(f"→ ERROR: {e}\n")
            results.append(
                {"id": lid, "title": name, "action": "review", "note": str(e)}
            )

        # Report every 50
        if stats["total"] % 50 == 0:
            pct = int(stats["found"] / max(stats["total"], 1) * 100)
            sys.stderr.write(
                f"\n=== BATCH {stats['total']}: found={stats['found']}({pct}%) hf={stats['hard_map']} nf={stats['not_found']} err={stats['errors']} ===\n"
            )

    print(
        json.dumps({"stats": stats, "results": results}, ensure_ascii=False, indent=2)
    )


if __name__ == "__main__":
    main()
