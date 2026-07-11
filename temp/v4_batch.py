"""v4: Batch location validator using English->Spanish scripture cross-reference.

Strategy:
1. Search English scriptures for the name → get reference + snippet
2. Extract a unique phrase from the English snippet (2-3 words excluding the name)
3. Search Spanish scriptures with that phrase → get Spanish snippet
4. Extract the Spanish form of the name from context
"""

import json, sys, urllib.request, re, time

BATCH_SIZE = 5
SOURCE_EN = "en/scriptures"
SOURCE_ES = "es/scriptures"


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


def find_spanish_form(name, english_text, english_ref):
    """Given English context, find the Spanish form."""
    # Get a unique phrase from English text (2-4 words, not including the name)
    words = english_text.replace(".", " ").replace(",", " ").split()
    # Remove name from words
    name_lower = name.lower()
    filtered = [w for w in words if w.lower() != name_lower and len(w) > 3]

    if not filtered:
        return None, "no_unique_phrase"

    # Try progressively longer phrases
    for phrase_len in [2, 3, 4]:
        for i in range(len(filtered) - phrase_len + 1):
            phrase = " ".join(filtered[i : i + phrase_len])
            if len(phrase) < 5:
                continue
            r = mcp_call(
                "search_text", {"query": phrase, "limit": 3, "source_filter": SOURCE_ES}
            )
            time.sleep(0.1)
            if r and r.get("results"):
                for hit in r["results"]:
                    # Check if this is near the same reference
                    if english_ref:
                        eng_chapter = re.search(r"(\d+):", english_ref)
                        if eng_chapter:
                            eng_num = eng_chapter.group(1)
                            sp_ref = hit.get("reference", "")
                            sp_chapter = re.search(r"(\d+):", sp_ref)
                            if sp_chapter and sp_chapter.group(1) == eng_num:
                                # Same chapter! Extract Spanish form
                                es_text = hit["text"]
                                es_lines = es_text.split(chr(10))
                                for line in es_lines:
                                    for candidate in gen_candidates(name):
                                        if candidate.lower() in line.lower():
                                            # Found it!
                                            actual = extract_exact_form(line, name)
                                            if actual:
                                                return actual, f"match_chapter {sp_ref}"
                                # Try broader: entire text
                                text_lower = es_text.lower()
                                for candidate in gen_candidates(name):
                                    idx = text_lower.find(candidate.lower())
                                    if idx >= 0:
                                        # Return 20 chars around it
                                        start = max(0, idx - 3)
                                        end = min(
                                            len(es_text), idx + len(candidate) + 3
                                        )
                                        found = es_text[start:end]
                                        return found, f"match_chapter_approx {sp_ref}"
                    # Fallback: check entire text for name
                    es_text = hit["text"]
                    for candidate in gen_candidates(name):
                        if candidate.lower() in es_text.lower():
                            return extract_exact_form(
                                es_text, name
                            ) or candidate, f"match_any {hit.get('reference', '')}"
    return None, "no_match_in_phrase_search"


def gen_candidates(name):
    """Generate likely Spanish spelling candidates."""
    candidates = set()
    base = name.rstrip(" 0123456789").strip()
    candidates.add(base)
    candidates.add(base.lower().capitalize())

    # Drop trailing 'h'
    if base.endswith("h"):
        candidates.add(base[:-1])
    if base.endswith("h") and base[:-1].endswith(("a", "e", "i", "o", "u")):
        candidates.add(base[:-1] + "á")
        candidates.add(base[:-1] + "é")

    # ph -> f
    if "ph" in base.lower():
        candidates.add(base.lower().replace("ph", "f").capitalize())
        candidates.add(base.lower().replace("Ph", "F").capitalize())

    # th -> t
    if "th" in base.lower():
        candidates.add(base.lower().replace("th", "t").capitalize())

    # sh -> s
    if "sh" in base.lower():
        candidates.add(base.lower().replace("sh", "s").capitalize())

    # ch -> c or qu
    if "ch" in base.lower():
        candidates.add(base.lower().replace("ch", "c").capitalize())

    # y -> i
    if base.endswith("y"):
        candidates.add(base[:-1] + "i")
        candidates.add(base[:-1] + "í")

    # Drop final double consonant
    if len(base) > 3 and base[-1] == base[-2]:
        candidates.add(base[:-1])

    # Add accent on final vowel for Spanish-like names
    if base.endswith("a"):
        candidates.add(base[:-1] + "á")
    elif base.endswith("e"):
        candidates.add(base[:-1] + "é")
    elif base.endswith("i"):
        candidates.add(base[:-1] + "í")
    elif base.endswith("o"):
        candidates.add(base[:-1] + "ó")

    # Remove 'h' after 'c','t','p' at start (Ash-dod, Beth-el patterns)
    for prefix in ["Ash", "Beth", "Ches", "Eph", "Gath", "Mizp", "Rabb", "Shib"]:
        if base.startswith(prefix):
            without_h = prefix.replace("h", "") + base[len(prefix) :]
            candidates.add(without_h)
            break

    return sorted(candidates, key=len, reverse=True)


def extract_exact_form(text, original_name):
    """Extract the exact Spanish form from text context."""
    base = original_name.rstrip(" 0123456789").strip()
    text_lower = text.lower()
    for candidate in gen_candidates(original_name):
        # Try to find candidate with word boundaries
        pattern = (
            r"(?<![a-zA-Záéíóúñ])"
            + re.escape(candidate.lower())
            + r"(?![a-zA-Záéíóúñ])"
        )
        m = re.search(pattern, text_lower)
        if m:
            return text[m.start() : m.end()]
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

    base = name.rstrip(" 0123456789").strip()

    # 1. Search English scriptures
    r_en = mcp_call(
        "search_text", {"query": base, "limit": 3, "source_filter": SOURCE_EN}
    )
    if not r_en or not r_en.get("results"):
        result["note"] = "not_in_english_scriptures"
        return result

    hit_en = r_en["results"][0]
    ref_en = hit_en.get("reference", "")
    text_en = hit_en["text"]

    result["en_ref"] = ref_en

    # 2. Find Spanish form
    sp_form, sp_note = find_spanish_form(base, text_en, ref_en)

    if sp_form:
        result["action"] = "update"
        # Clean the form - get just the name, not surrounding text
        clean = sp_form.strip(".,;:!?¿¡\"'()[]{} \t\n\r")
        # If it's long, try to extract just the name
        if len(clean) > 50:
            for cand in gen_candidates(name):
                if cand.lower() in clean.lower():
                    clean = cand
                    break
        # Try to match original name structure
        orig_words = name.split()
        if len(orig_words) > 1 and orig_words[-1].isdigit():
            clean = clean + " " + orig_words[-1]
        result["spanish"] = clean
        result["note"] = sp_note
    else:
        result["note"] = f"no_spanish_match|{sp_note}|{ref_en}"

    return result


def main():
    locations = json.loads(sys.stdin.read())

    # Get only review items from previous run
    all_results = []
    stats = {"total": 0, "found": 0, "not_found": 0, "errors": 0}

    for idx, loc in enumerate(locations):
        title = loc["post_title"].strip()
        loc_id = loc["ID"]

        # Only process if title has no accents (was in review)
        if any(ord(c) > 191 for c in title):
            continue

        stats["total"] += 1
        sys.stderr.write(f"[{stats['total']}] {title}... ")
        sys.stderr.flush()

        try:
            r = process_name(title, loc_id)
            all_results.append(r)
            if r["action"] == "update":
                stats["found"] += 1
                sys.stderr.write(f"→ {r['spanish']} ({r['note']})\n")
            else:
                stats["not_found"] += 1
                sys.stderr.write(f"→ {r['note']}\n")
        except Exception as e:
            stats["errors"] += 1
            sys.stderr.write(f"→ ERROR: {e}\n")
            all_results.append(
                {
                    "id": loc_id,
                    "title": title,
                    "action": "review",
                    "spanish": None,
                    "note": str(e),
                }
            )

        if (idx + 1) % BATCH_SIZE == 0:
            sys.stderr.write(f"--- Batch checkpoint: {idx + 1} processed ---\n")

    print(
        json.dumps(
            {"stats": stats, "results": all_results}, ensure_ascii=False, indent=2
        )
    )


if __name__ == "__main__":
    main()
