"""Strategy v2: use nearby proper nouns as context anchors."""

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


HEBREW_BIBLE = {
    "Naioth": "Naiot",
    "Baal-tamar": "Baal-tamar",
    "Eben-ezer": "Eben-ezer",
    "Philistines": "Filisteos",
    "Dagon": "Dagón",
    "Hammath": "Hamat",
    "Rakkath": "Racat",
    "Chinnereth": "Cineret",
    "Adamah": "Adama",
}


def get_spanish_proper(en_word):
    if en_word in HEBREW_BIBLE:
        return HEBREW_BIBLE[en_word]
    return en_word


def find_spanish_form_anchor(name):
    print(f"\n=== {name} ===")

    # 1. English lookup
    r = mc("search_text", {"query": name, "limit": 1, "source_filter": "en/scriptures"})
    if not r or not r.get("results"):
        print("  NOT IN ENGLISH SCRIPTURES")
        return []

    hit = r["results"][0]
    ref = hit.get("reference", "")
    fpath = hit.get("file_path", "")
    text_en = hit["text"]
    print(f"  EN: {ref} ({fpath.split('/')[-1]})")

    # 2. Get proper nouns from context
    words = re.findall(r"\b[A-Z][a-z]+(?:-[a-z]+)*\b", text_en)
    proper = [w for w in words if w.lower() != name.lower() and len(w) > 2]
    print(f"  Context names: {proper[:5]}")

    # 3. Get book folder
    book_folder = None
    m = re.search(r"en/scriptures/[^/]+/([^/]+)/", fpath)
    if m:
        book_folder = m.group(1)

    # 4. Get chapter
    chapter = None
    cm = re.search(r"(\d+):", ref)
    if cm:
        chapter = cm.group(1)

    # 5. Generate Spanish candidates
    candidates = set()
    base = name.rstrip(" 0123456789")

    # Original
    candidates.add(base)

    # Drop final h
    if base.endswith("h"):
        candidates.add(base[:-1])
        if base[:-1][-1] in "aeio":
            candidates.add(base[:-1] + "á")

    # ph->f
    if "ph" in base:
        candidates.add(base.replace("ph", "f"))

    # th->t
    if "th" in base:
        candidates.add(base.replace("th", "t"))

    # sh->s
    if "sh" in base.lower():
        candidates.add(base.lower().replace("sh", "s").capitalize())

    # Accent tests
    for v in "aeio":
        if base.endswith(v):
            candidates.add(
                base[:-1] + "á"
                if v == "a"
                else base[:-1] + "é"
                if v == "e"
                else base[:-1] + "í"
                if v == "i"
                else base[:-1] + "ó"
            )

    results = []

    # Strategy A: Search with context proper noun + name
    for pn in proper[:3]:
        sp_pn = get_spanish_proper(pn)
        for cand in sorted(candidates, key=len, reverse=True):
            query = f"{sp_pn} {cand}"
            rs = mc(
                "search_text",
                {"query": query, "limit": 2, "source_filter": "es/scriptures"},
            )
            if rs and rs.get("results"):
                for hit2 in rs["results"]:
                    fpath2 = hit2.get("file_path", "")
                    if book_folder and book_folder not in fpath2:
                        continue
                    results.append(
                        {
                            "candidate": cand,
                            "ref": hit2.get("reference", ""),
                            "fpath": fpath2,
                            "text": hit2["text"][:300],
                            "strategy": "A_context",
                        }
                    )
                    print(
                        f"  Strategy A: '{query}' → {hit2.get('reference', '')} → {cand}"
                    )

    # Strategy B: Search Spanish for candidate, same book
    if not results:
        for cand in sorted(candidates, key=len, reverse=True):
            if len(cand) < 3:
                continue
            rs = mc(
                "search_text",
                {"query": cand, "limit": 3, "source_filter": "es/scriptures"},
            )
            if rs and rs.get("results"):
                for hit2 in rs["results"]:
                    fpath2 = hit2.get("file_path", "")
                    if book_folder and book_folder not in fpath2:
                        continue
                    results.append(
                        {
                            "candidate": cand,
                            "ref": hit2.get("reference", ""),
                            "fpath": fpath2,
                            "text": hit2["text"][:300],
                            "strategy": "B_direct",
                        }
                    )
                    print(f"  Strategy B: '{cand}' → {hit2.get('reference', '')}")

    # Strategy C: Search for unique context proper noun, then scan
    if not results:
        for pn in proper[:3]:
            sp_pn = get_spanish_proper(pn)
            rs = mc(
                "search_text",
                {"query": sp_pn, "limit": 3, "source_filter": "es/scriptures"},
            )
            if rs and rs.get("results"):
                for hit2 in rs["results"]:
                    fpath2 = hit2.get("file_path", "")
                    if book_folder and book_folder not in fpath2:
                        continue
                    txt = hit2["text"]
                    # Search for any candidate in the text
                    for cand in sorted(candidates, key=len, reverse=True):
                        pat = (
                            r"(?<![a-zA-Záéíóúñ])"
                            + re.escape(
                                re.sub(
                                    r"[áéíóú]",
                                    lambda m: {
                                        "á": "a",
                                        "é": "e",
                                        "í": "i",
                                        "ó": "o",
                                        "ú": "u",
                                    }[m.group()],
                                    cand.lower(),
                                )
                            )
                            + r"(?![a-zA-Záéíóúñ])"
                        )
                        if re.search(pat, txt.lower()):
                            # Get exact form from text
                            idx = txt.lower().find(
                                re.sub(
                                    r"[áéíóú]",
                                    lambda m: {
                                        "á": "a",
                                        "é": "e",
                                        "í": "i",
                                        "ó": "o",
                                        "ú": "u",
                                    }[m.group()],
                                    cand.lower(),
                                )
                            )
                            actual = txt[idx : idx + len(cand) + 3]
                            results.append(
                                {
                                    "candidate": cand,
                                    "actual": actual,
                                    "ref": hit2.get("reference", ""),
                                    "fpath": fpath2,
                                    "text": txt[:300],
                                    "strategy": "C_scan",
                                }
                            )
                            print(
                                f"  Strategy C: '{sp_pn}' → found '{cand}' in {hit2.get('reference', '')}"
                            )
                            break

    if not results:
        print("  NO MATCH")
    else:
        print(f"  Best: {results[0]}")

    return results


# Test
for name in [
    "Ramah",
    "Gibeah",
    "Zorah",
    "Sharon",
    "Shur",
    "Sinai",
    "Tirzah",
    "Succoth",
]:
    find_spanish_form_anchor(name)
