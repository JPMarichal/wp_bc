"""Debug: smarter search for Spanish equivalents."""

import json, urllib.request, re


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


def try_find_spanish(name):
    """Try multiple strategies to find Spanish form."""
    print(f"\n=== Finding Spanish for: {name} ===")

    # Step 1: Search English to get reference
    r = mc("search_text", {"query": name, "limit": 1, "source_filter": "en/scriptures"})
    if not r or not r.get("results"):
        print("  NOT FOUND in English scriptures")
        return None

    hit = r["results"][0]
    ref = hit.get("reference", "")
    fpath = hit.get("file_path", "")
    text_en = hit["text"]
    print(f"  English: {ref} ({fpath})")
    print(f"  Snippet: {text_en[:150]}...")

    # Step 2: Get unique proper nouns from English text
    words = re.findall(r"\b[A-Z][a-z]+\b", text_en)
    proper_nouns = [w for w in words if w.lower() != name.lower() and len(w) > 2]
    print(f"  Proper nouns: {proper_nouns[:5]}")

    # Step 3: Extract chapter info
    ch_match = re.search(r"(\d+):(\d+)", ref)
    if ch_match:
        chapter = ch_match.group(1)
    else:
        chapter = None

    # Step 4: Extract book name from file_path
    book_match = re.search(r"en/scriptures/[^/]+/([^/]+)/", fpath)
    if book_match:
        book_folder = book_match.group(1)
        print(f"  Book folder: {book_folder}")
    else:
        book_folder = None

    # Step 5: Try searching Spanish with proper nouns + candidate
    if proper_nouns:
        for pn in proper_nouns[:3]:
            query = f"{pn} {name}"
            rs = mc(
                "search_text",
                {"query": query, "limit": 3, "source_filter": "es/scriptures"},
            )
            if rs and rs.get("results"):
                for hit2 in rs["results"]:
                    ref2 = hit2.get("reference", "")
                    text_es = hit2["text"]
                    # Check if same chapter
                    ch2_match = re.search(r"(\d+):", ref2)
                    if chapter and ch2_match and ch2_match.group(1) == chapter:
                        print(f"  SAME CHAPTER! Spanish ref: {ref2}")
                        print(f"  Spanish text: {text_es[:200]}")
                        return text_es[:500]
                    # Check book folder
                    fpath2 = hit2.get("file_path", "")
                    if book_folder and book_folder in fpath2:
                        print(f"  SAME BOOK! Spanish ref: {ref2}")
                        print(f"  Spanish text: {text_es[:200]}")
                        return text_es[:500]

    # Step 6: Fallback - search Spanish with name candidates
    candidates = [
        name,
        name.rstrip("h"),
        name.rstrip("h") + "á",
        name.replace("h", ""),
        name.lower().capitalize(),
    ]
    for c in candidates:
        if len(c) < 3:
            continue
        rs = mc(
            "search_text", {"query": c, "limit": 3, "source_filter": "es/scriptures"}
        )
        if rs and rs.get("results"):
            for hit2 in rs["results"]:
                ref2 = hit2.get("reference", "")
                fpath2 = hit2.get("file_path", "")
                if book_folder and book_folder in fpath2:
                    print(f"  SAME BOOK via candidate '{c}': {ref2}")
                    print(f"  Spanish text: {hit2['text'][:200]}")
                    return hit2["text"][:500]

    print("  No Spanish match found")
    return None


# Test with several names
for name in ["Ramah", "Gibeah", "Ashdod", "Zorah", "Sharon"]:
    try_find_spanish(name)
