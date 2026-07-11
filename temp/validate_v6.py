"""v6: Location translation using original passage references (gnosis-new.tsv).

Strategy:
1. Load gnosis-new.tsv → {Name: [verse_refs]}
2. For each review location, find its verse from gnosis
3. Convert OSIS abbrev (e.g. "1Sam.19.19") → book_folder/chapter
4. Search Spanish at that exact chapter file
5. Extract Spanish form from text context
"""

import json, sys, urllib.request, re, csv, io

OSIS_MAP = {
    "Gen": "genesis",
    "Ex": "exodus",
    "Lev": "leviticus",
    "Num": "numbers",
    "Deut": "deuteronomy",
    "Josh": "joshua",
    "Judg": "judges",
    "Ruth": "ruth",
    "1Sam": "1-samuel",
    "2Sam": "2-samuel",
    "1Kgs": "1-kings",
    "2Kgs": "2-kings",
    "1Chr": "1-chronicles",
    "2Chr": "2-chronicles",
    "Ezra": "ezra",
    "Neh": "nehemiah",
    "Est": "esther",
    "Job": "job",
    "Ps": "psalms",
    "Prov": "proverbs",
    "Eccl": "ecclesiastes",
    "Song": "song-of-solomon",
    "Isa": "isaiah",
    "Jer": "jeremiah",
    "Lam": "lamentations",
    "Ezek": "ezekiel",
    "Dan": "daniel",
    "Hos": "hosea",
    "Joel": "joel",
    "Amos": "amos",
    "Obad": "obadiah",
    "Jonah": "jonah",
    "Mic": "micah",
    "Nah": "nahum",
    "Hab": "habakkuk",
    "Zeph": "zephaniah",
    "Hag": "haggai",
    "Zech": "zechariah",
    "Mal": "malachi",
    "Matt": "matthew",
    "Mark": "mark",
    "Luke": "luke",
    "John": "john",
    "Acts": "acts",
    "Rom": "romans",
    "1Cor": "1-corinthians",
    "2Cor": "2-corinthians",
    "Gal": "galatians",
    "Eph": "ephesians",
    "Phil": "philippians",
    "Col": "colossians",
    "1Thess": "1-thessalonians",
    "2Thess": "2-thessalonians",
    "1Tim": "1-timothy",
    "2Tim": "2-timothy",
    "Titus": "titus",
    "Phlm": "philemon",
    "Heb": "hebrews",
    "Jas": "james",
    "1Pet": "1-peter",
    "2Pet": "2-peter",
    "1Jn": "1-john",
    "2Jn": "2-john",
    "3Jn": "3-john",
    "Jude": "jude",
    "Rev": "revelation",
}

ES_MAP = {
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
    "Zorah 2": "Zora 2",
    "Sinai": "Sinaí",
    "Sinai 2": "Sinaí 2",
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
    "Aram": "Aram",
    "Elam": "Elam",
    "Chaldea": "Caldea",
    "Moab": "Moab",
    "Ammon": "Amón",
    "Edom": "Edom",
    "Canaan": "Canaán",
    "Philistia": "Filistea",
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


def load_gnosis(path):
    """Load gnosis-new.tsv into {name_lower: [verse_refs]}"""
    gnosis = {}
    with open(path, "r", encoding="utf-8") as f:
        reader = csv.DictReader(f, delimiter="\t")
        for row in reader:
            name = row["Name"].strip()
            verses = row.get("Verses", "").strip()
            if verses and name:
                parts = [v.strip() for v in verses.split(",") if v.strip()]
                key = name.lower()
                if key in gnosis:
                    gnosis[key]["verses"].extend(parts)
                else:
                    gnosis[key] = {"name": name, "verses": parts}
    return gnosis


def find_gnosis_entry(gnosis, name):
    """Find gnosis entry for a given name (fuzzy match)."""
    base = name.rstrip(" 0123456789").strip().lower()
    if base in gnosis:
        return gnosis[base]
    # Try without trailing 'h'
    if base.endswith("h") and base[:-1] in gnosis:
        return gnosis[base[:-1]]
    # Try partial match
    for key, val in gnosis.items():
        if key.startswith(base) or base.startswith(key):
            return val
    return None


def parse_verse(ref):
    """Parse '1Sam.19.19' → ('1-samuel', '19', '19') or None."""
    parts = ref.split(".")
    if len(parts) < 2:
        return None
    book_osis = parts[0]
    chapter = parts[1]
    book_en = OSIS_MAP.get(book_osis)
    if not book_en:
        return None
    # Only test/nt - skip non-standard
    for prefix in [
        "1-nephi",
        "2-nephi",
        "words-of",
        "3-nephi",
        "4-nephi",
        "ether",
        "moroni",
        "doctrine-and-covenants",
    ]:
        if book_en.startswith(prefix):
            return None
    book_es = ES_MAP.get(book_en, book_en)
    testament = (
        "ot"
        if book_en
        in [
            "genesis",
            "exodus",
            "leviticus",
            "numbers",
            "deuteronomy",
            "joshua",
            "judges",
            "ruth",
            "1-samuel",
            "2-samuel",
            "1-kings",
            "2-kings",
            "1-chronicles",
            "2-chronicles",
            "ezra",
            "nehemiah",
            "esther",
            "job",
            "psalms",
            "proverbs",
            "ecclesiastes",
            "song-of-solomon",
            "isaiah",
            "jeremiah",
            "lamentations",
            "ezekiel",
            "daniel",
            "hosea",
            "joel",
            "amos",
            "obadiah",
            "jonah",
            "micah",
            "nahum",
            "habakkuk",
            "zephaniah",
            "haggai",
            "zechariah",
            "malachi",
        ]
        else "nt"
    )
    return (book_en, book_es, testament, chapter)


def find_in_chapter(name, es_fpath):
    """Search for name variants in a Spanish chapter file."""
    base = name.rstrip(" 0123456789")
    candidates = set()
    candidates.add(base)
    if base.endswith("h"):
        bh = base[:-1]
        candidates.add(bh)
        if bh[-1] == "a":
            candidates.add(bh[:-1] + "á")
        elif bh[-1] == "e":
            candidates.add(bh[:-1] + "é")
        elif bh[-1] == "i":
            candidates.add(bh[:-1] + "í")
        elif bh[-1] == "o":
            candidates.add(bh[:-1] + "ó")
    else:
        for v, a in [("a", "á"), ("e", "é"), ("i", "í"), ("o", "ó")]:
            if base.endswith(v):
                candidates.add(base[:-1] + a)

    for cand in sorted(candidates, key=len, reverse=True):
        if len(cand) < 3:
            continue
        rs = mc("search_text", {"query": cand, "limit": 3, "source_filter": es_fpath})
        if not rs or not rs.get("results"):
            continue
        for hit in rs["results"]:
            text = hit["text"]
            for form in [cand, cand[0].upper() + cand[1:], cand.lower()]:
                if len(form) < 3:
                    continue
                pat = (
                    r"(?<![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                    + re.escape(form)
                    + r"(?![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                )
                m = re.search(pat, text)
                if m:
                    # Verify it looks like a proper noun
                    ctx = text[max(0, m.start() - 3) : m.start()]
                    if not ctx or ctx[-1] in " \n\t\r":
                        return m.group()
    return None


def process(loc, gnosis):
    name = loc["post_title"].strip()
    lid = loc["ID"]
    base = name.rstrip(" 0123456789")
    suffix = name[len(base) :]

    result = {"id": lid, "title": name, "action": "review", "spanish": None}

    # 1. HARD_MAP
    if base in HARD_MAP:
        result["action"] = "update"
        result["spanish"] = HARD_MAP[base] + suffix
        result["note"] = "hard_map"
        return result

    # 2. Find gnosis entry with verses
    entry = find_gnosis_entry(gnosis, name)
    if not entry or not entry["verses"]:
        # Fallback: search English for passage, then Spanish same chapter
        r = mc(
            "search_text", {"query": base, "limit": 1, "source_filter": "en/scriptures"}
        )
        if r and r.get("results"):
            hit = r["results"][0]
            fpath = hit.get("file_path", "")
            m = re.search(r"en/scriptures/([^/]+)/([^/]+)/(\d+)\.txt", fpath)
            if m:
                testament = m.group(1)
                book_en = m.group(2)
                chapter = m.group(3)
                book_es = ES_MAP.get(book_en, book_en)
                es_fpath = f"es/scriptures/{testament}/{book_es}/{chapter}.txt"
                found = find_in_chapter(name, es_fpath)
                if found:
                    result["action"] = "update"
                    result["spanish"] = found + suffix
                    result["note"] = f"en_fallback|{fpath}"
                    return result
        result["note"] = "no_gnosis_entry"
        return result

    # 3. Try each verse reference
    tried = set()
    for ref in entry["verses"]:
        parsed = parse_verse(ref)
        if not parsed:
            continue
        book_en, book_es, testament, chapter = parsed
        es_fpath = f"es/scriptures/{testament}/{book_es}/{chapter}.txt"
        if es_fpath in tried:
            continue
        tried.add(es_fpath)

        found = find_in_chapter(name, es_fpath)
        if found:
            result["action"] = "update"
            result["spanish"] = found + suffix
            result["note"] = f"verse|{ref}|{es_fpath}"
            return result

    # 4. Fallback: search broader (whole book, then all)
    if entry["verses"]:
        ref = entry["verses"][0]
        parsed = parse_verse(ref)
        if parsed:
            book_en, book_es, testament, chapter = parsed
            es_book = f"es/scriptures/{testament}/{book_es}"
            found = find_in_chapter(name, es_book)
            if found:
                result["action"] = "update"
                result["spanish"] = found + suffix
                result["note"] = f"book_fallback|{es_book}"
                return result

    result["note"] = (
        f"no_es_match|{entry['verses'][0] if entry['verses'] else 'no_verses'}"
    )
    return result


def main():
    gnosis_path = "/tmp/gnosis-new.tsv"
    if len(sys.argv) > 1:
        gnosis_path = sys.argv[1]

    gnosis = load_gnosis(gnosis_path)
    sys.stderr.write(f"Loaded {len(gnosis)} gnosis entries\n")

    locations = json.loads(sys.stdin.read())
    stats = {
        "total": 0,
        "found": 0,
        "not_found": 0,
        "hard_map": 0,
        "no_gnosis": 0,
        "errors": 0,
    }
    results = []

    to_process = [
        l for l in locations if not any(ord(c) > 191 for c in l["post_title"])
    ]

    for idx, loc in enumerate(to_process):
        name = loc["post_title"]
        lid = loc["ID"]
        stats["total"] += 1

        try:
            r = process(loc, gnosis)
            results.append(r)
            if r["action"] == "update":
                stats["found"] += 1
                if r.get("note", "").startswith("hard_map"):
                    stats["hard_map"] += 1
            else:
                stats["not_found"] += 1
                if r.get("note") == "no_gnosis_entry":
                    stats["no_gnosis"] += 1
        except Exception as e:
            stats["errors"] += 1
            results.append(
                {"id": lid, "title": name, "action": "review", "note": str(e)}
            )

        if stats["total"] % 10 == 0 or stats["total"] == len(to_process):
            pct = int(stats["found"] / max(stats["total"], 1) * 100)
            sys.stderr.write(
                f"[{stats['total']}/{len(to_process)}] found={stats['found']}({pct}%) hf={stats['hard_map']} nog={stats['no_gnosis']} nf={stats['not_found']} err={stats['errors']}\n"
            )

    print(
        json.dumps({"stats": stats, "results": results}, ensure_ascii=False, indent=2)
    )


if __name__ == "__main__":
    main()
