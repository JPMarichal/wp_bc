"""v4: Cross-reference English → Spanish using book-context search.

Strategy:
1. Search English scriptures for name → get file_path, book_folder, chapter
2. Generate Spanish accent candidates (Rama, Ramá, etc.)
3. Search Spanish scriptures (filtered to same book) for each candidate
4. Verify candidate appears as proper noun (capitalized) in result
5. Extract exact Spanish form
"""

import json, sys, urllib.request, re, time


def mcp_call(method, params):
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


def gen_spanish_candidates(name):
    """Generate likely Spanish spelling candidates."""
    base = name.rstrip(" 0123456789")
    candidates = set()
    candidates.add(base)
    # Drop final 'h'
    if base.endswith("h"):
        base_wo_h = base[:-1]
        candidates.add(base_wo_h)
        candidates.add(base_wo_h + "á")
        candidates.add(base_wo_h + "é")
        candidates.add(base_wo_h + "í")
        candidates.add(base_wo_h + "ó")
    else:
        # Add accents on final vowel
        for v, acc in [("a", "á"), ("e", "é"), ("i", "í"), ("o", "ó"), ("u", "ú")]:
            if base.endswith(v):
                candidates.add(base[:-1] + acc)
    # ph→f, th→t, sh→s
    if "ph" in base:
        candidates.add(base.replace("ph", "f"))
    if "th" in base:
        candidates.add(base.replace("th", "t"))
    if "sh" in base.lower():
        candidates.add(base.lower().replace("sh", "s").capitalize())
    if "ch" in base.lower():
        candidates.add(base.lower().replace("ch", "c").capitalize())
    # y→i
    if base.endswith("y"):
        candidates.add(base[:-1] + "i")
        candidates.add(base[:-1] + "í")
    # Remove doubled final consonant
    if len(base) > 2 and base[-1] == base[-2]:
        candidates.add(base[:-1])
    return sorted(candidates, key=len, reverse=True)


def text_contains_as_proper(text, candidate):
    """Check if candidate appears as a proper noun (capitalized standalone)."""
    # Look for candidate at start of sentence (after number or newline)
    patterns = [
        rf"\b{candidate}\b",
    ]
    for p in patterns:
        m = re.search(p, text)
        if m:
            # Check if it's capitalized
            before = text[max(0, m.start() - 3) : m.start()]
            # If preceded by space, newline, or number, and candidate starts with uppercase
            if candidate[0].isupper() and (
                not before or before.strip() == "" or before.strip()[-1] in " \n\r\t"
            ):
                return m.group()
    return None


def process_name(name, loc_id):
    """Process a single name."""
    result = {
        "id": loc_id,
        "title": name,
        "action": "review",
        "spanish": None,
        "note": "",
    }
    base = name.rstrip(" 0123456789")

    # 1. Search English
    r_en = mcp_call(
        "search_text", {"query": base, "limit": 1, "source_filter": "en/scriptures"}
    )
    if not r_en or not r_en.get("results"):
        result["note"] = "no_en_match"
        return result

    hit_en = r_en["results"][0]
    ref_en = hit_en.get("reference", "")
    fpath_en = hit_en.get("file_path", "")

    m = re.search(r"en/scriptures/[^/]+/([^/]+)/", fpath_en)
    book_folder = m.group(1) if m else None

    if not book_folder:
        result["note"] = "no_book"
        return result

    result["en_ref"] = ref_en
    result["book"] = book_folder

    # 2. Generate candidates and search Spanish
    candidates = gen_spanish_candidates(name)
    es_filter = (
        f"es/scriptures/ot/{book_folder}"
        if "/ot/" in fpath_en
        else f"es/scriptures/nt/{book_folder}"
        if "/nt/" in fpath_en
        else f"es/scriptures/{book_folder}"
    )

    # Also try broader search if book-specific fails
    best_match = None

    for source_filter in [es_filter, "es/scriptures"]:
        for cand in candidates:
            if len(cand) < 3:
                continue
            r_es = mcp_call(
                "search_text",
                {"query": cand, "limit": 3, "source_filter": source_filter},
            )
            if not r_es or not r_es.get("results"):
                continue
            for hit in r_es["results"]:
                text_es = hit["text"]
                # Check each candidate variant (capitalized)
                for c in [cand, cand.lower(), cand.capitalize(), cand.upper()]:
                    if len(c) < 3:
                        continue
                    found = text_contains_as_proper(text_es, c)
                    if found:
                        best_match = found
                        result["action"] = "update"
                        result["spanish"] = found
                        result["note"] = f"match:{hit.get('reference', '')}"
                        return result
            time.sleep(0.05)

    if not best_match:
        # Last resort: try without accent stripping, check any capitalized form
        for source_filter in [es_filter, "es/scriptures"]:
            r_es = mcp_call(
                "search_text",
                {
                    "query": base.replace("h", ""),
                    "limit": 5,
                    "source_filter": source_filter,
                },
            )
            if r_es and r_es.get("results"):
                for hit in r_es["results"]:
                    text_es = hit["text"]
                    # Look for any capitalized word similar to base
                    for word in re.findall(
                        r"\b[A-Z][a-záéíóú]+(?:\s[A-Z][a-záéíóú]+)?\b", text_es
                    ):
                        w_clean = (
                            word.lower()
                            .replace("á", "a")
                            .replace("é", "e")
                            .replace("í", "i")
                            .replace("ó", "o")
                            .replace("ú", "u")
                        )
                        b_clean = base.lower().replace("h", "")
                        if w_clean == b_clean or (
                            len(w_clean) > 3
                            and len(b_clean) > 3
                            and (
                                w_clean.startswith(b_clean)
                                or b_clean.startswith(w_clean)
                            )
                        ):
                            best_match = word
                            result["action"] = "update"
                            result["spanish"] = word
                            result["note"] = f"fuzzy:{hit.get('reference', '')}"
                            return result

    if best_match:
        result["action"] = "update"
        result["spanish"] = best_match
        result["note"] = "match_found"
    else:
        result["note"] = f"no_es_match|{ref_en}"

    return result


def main():
    locations = json.loads(sys.stdin.read())
    stats = {"total": 0, "found": 0, "not_found": 0, "errors": 0}
    results_out = []

    for loc in locations:
        title = loc["post_title"].strip()
        loc_id = loc["ID"]
        # Skip already-accented (already Spanish)
        if any(ord(c) > 191 for c in title):
            continue

        stats["total"] += 1
        sys.stderr.write(f"[{stats['total']}] {title}... ")
        sys.stderr.flush()

        try:
            r = process_name(title, loc_id)
            results_out.append(r)
            if r["action"] == "update":
                stats["found"] += 1
                sys.stderr.write(f"→ {r['spanish']} ({r['note']})\n")
            else:
                stats["not_found"] += 1
                sys.stderr.write(f"→ {r['note']}\n")
        except Exception as e:
            stats["errors"] += 1
            sys.stderr.write(f"→ ERROR: {e}\n")
            results_out.append(
                {"id": loc_id, "title": title, "action": "review", "note": str(e)}
            )

        if stats["total"] % 10 == 0:
            sys.stderr.write(
                f"--- Checkpoint: {stats['total']} processed, {stats['found']} found ---\n"
            )

    print(
        json.dumps(
            {"stats": stats, "results": results_out}, ensure_ascii=False, indent=2
        )
    )


if __name__ == "__main__":
    main()
