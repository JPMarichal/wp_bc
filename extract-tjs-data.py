import json, re, urllib.request, os, sys, time, base64, html as html_mod

BASE_URL = "https://www.churchofjesuschrist.org/study/scriptures/jst/JST"


def fetch_url(url):
    req = urllib.request.Request(
        url,
        headers={
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language": "es-ES,es;q=0.9,en;q=0.8",
        },
    )
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            return resp.read().decode("utf-8", errors="replace")
    except Exception as e:
        print(f"  ERROR fetching: {e}")
        return None


def extract_body_from_state(html):
    m = re.search(r'window\.__INITIAL_STATE__="([^"]+)"', html)
    if not m:
        print("    No __INITIAL_STATE__ found")
        return None
    encoded = m.group(1)
    try:
        decoded = base64.b64decode(encoded).decode("utf-8")
        data = json.loads(decoded)
        cs = data.get("reader", {}).get("contentStore", {})
        for key, entry in cs.items():
            body = entry.get("content", {}).get("body", "")
            if body:
                return body
        return None
    except Exception as e:
        print(f"    Error parsing state: {e}")
        return None


def parse_verses_from_html(body):
    # body is HTML string with <p class="verse"> blocks
    # Each has <span class="verse-number">N </span> followed by verse text
    verses = {}
    pattern = re.compile(
        r'<p class="verse"[^>]*>.*?<span class="verse-number">\s*(\d+)\s*</span>\s*(.*?)</p>',
        re.DOTALL,
    )
    for m in pattern.finditer(body):
        vnum = int(m.group(1))
        text = m.group(2).strip()
        # Clean up HTML tags inside the text
        text = re.sub(r"<[^>]+>", "", text)
        text = html_mod.unescape(text)
        text = re.sub(r"\s+", " ", text).strip()
        if text:
            verses[vnum] = text

    return verses


MAPPING = {
    "genesis": "jst-gen",
    "exodo": "jst-ex",
    "deuteronomio": "jst-deut",
    "1-samuel": "jst-1-sam",
    "2-samuel": "jst-2-sam",
    "1-cronicas": "jst-1-chron",
    "2-cronicas": "jst-2-chron",
    "salmos": "jst-ps",
    "isaias": "jst-isa",
    "jeremias": "jst-jer",
    "amos": "jst-amos",
    "mateo": "jst-matt",
    "marcos": "jst-mark",
    "lucas": "jst-luke",
    "juan": "jst-john",
    "hechos": "jst-acts",
    "romanos": "jst-rom",
    "1-corintios": "jst-1-cor",
    "2-corintios": "jst-2-cor",
    "galatas": "jst-gal",
    "efesios": "jst-eph",
    "colosenses": "jst-col",
    "1-tesalonicenses": "jst-1-thes",
    "2-tesalonicenses": "jst-2-thes",
    "1-timoteo": "jst-1-tim",
    "hebreos": "jst-heb",
    "santiago": "jst-james",
    "1-pedro": "jst-1-pet",
    "2-pedro": "jst-2-pet",
    "1-juan": "jst-1-jn",
    "apocalipsis": "jst-rev",
}

CHAPTERS = {
    "genesis": [9, 14, 15, 17, 19, 21, 48, 50],
    "exodo": [4, 18, 22, 32, 33, 34],
    "deuteronomio": [10],
    "1-samuel": [16],
    "2-samuel": [12],
    "1-cronicas": [21],
    "2-cronicas": [18],
    "salmos": [11, 14, 24, 109],
    "isaias": [29, 42],
    "jeremias": [26],
    "amos": [7],
    "mateo": [3, 4, 5, 6, 7, 9, 11, 12, 13, 16, 17, 18, 19, 21, 23, 26, 27],
    "marcos": [2, 3, 7, 8, 9, 12, 14, 16],
    "lucas": [1, 2, 3, 6, 9, 11, 12, 14, 16, 17, 18, 21, 23, 24],
    "juan": [1, 4, 6, 13, 14],
    "hechos": [9, 22],
    "romanos": [3, 4, 7, 8, 13],
    "1-corintios": [7, 15],
    "2-corintios": [5],
    "galatas": [3],
    "efesios": [4],
    "colosenses": [2],
    "1-tesalonicenses": [4],
    "2-tesalonicenses": [2],
    "1-timoteo": [2, 3, 6],
    "hebreos": [1, 4, 6, 7, 11],
    "santiago": [1, 2],
    "1-pedro": [3, 4],
    "2-pedro": [3],
    "1-juan": [2, 3, 4],
    "apocalipsis": [1, 2, 5, 12, 19],
}

DATA_DIR = r"C:\own\wp_bc\wp-content\plugins\lds-passage-block\data\tjs"
os.makedirs(DATA_DIR, exist_ok=True)

book_data = {}

for book_slug, chapters in CHAPTERS.items():
    jst_abbr = MAPPING[book_slug]
    print(f"\n=== {book_slug} ({jst_abbr}) ===")

    for chapter in chapters:
        url = f"https://www.churchofjesuschrist.org/study/scriptures/jst/{jst_abbr}/{chapter}?lang=spa"
        print(f"  Cap. {chapter} ({url})")

        html = fetch_url(url)
        if not html:
            print(f"    No response")
            continue

        body = extract_body_from_state(html)
        if not body:
            print(f"    No body content found")
            continue

        verses = parse_verses_from_html(body)
        if verses:
            max_v = max(verses.keys())
            arr = [""] * max_v
            for vnum, text in verses.items():
                arr[vnum - 1] = text
            if book_slug not in book_data:
                book_data[book_slug] = {}
            book_data[book_slug][chapter] = arr
            print(f"    Found {len(verses)} verses: {sorted(verses.keys())}")
        else:
            print(f"    No verses found in body")

        time.sleep(0.3)

    if book_slug in book_data:
        filepath = os.path.join(DATA_DIR, f"{book_slug}.json")
        with open(filepath, "w", encoding="utf-8") as f:
            json.dump(book_data[book_slug], f, ensure_ascii=False, indent=2)
        print(f"  WRITTEN: {filepath} ({len(book_data[book_slug])} chapters)")

print("\n\n=== DONE ===")
