"""Debug: why Ramah search fails."""

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


name = "Ramah"
base = name.rstrip(" 0123456789")
print(f"Base: '{base}'")

# Generate candidates
candidates = set()
candidates.add(base)
if base.endswith("h"):
    base_wo_h = base[:-1]
    candidates.add(base_wo_h)
    if len(base_wo_h) > 0:
        for v, a in [("a", "á"), ("e", "é"), ("i", "í"), ("o", "ó")]:
            if base_wo_h.endswith(v):
                candidates.add(base_wo_h[:-1] + a)
print(f"Candidates: {candidates}")

# Test each candidate in 1-samuel
for cand in sorted(candidates, key=len, reverse=True):
    print(f"\nSearching for '{cand}' in es/scriptures/ot/1-samuel...")
    r = mc(
        "search_text",
        {"query": cand, "limit": 3, "source_filter": "es/scriptures/ot/1-samuel"},
    )
    if r and r.get("results"):
        for hit in r["results"][:2]:
            ref = hit.get("reference", "")
            text = hit["text"]
            # Check if candidate appears as proper noun
            pat = (
                r"(?<![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                + re.escape(cand)
                + r"(?![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
            )
            m = re.search(pat, text)
            if m:
                ctx = text[max(0, m.start() - 5) : m.end() + 5]
                print(f"  FOUND in {ref}: ...{ctx}...")
            else:
                # Check any accented variant
                for alt in [cand, cand.replace("a", "á").replace("e", "é")]:
                    p2 = (
                        r"(?<![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                        + re.escape(alt)
                        + r"(?![a-zA-ZáéíóúñÁÉÍÓÚÑ])"
                    )
                    m2 = re.search(p2, text)
                    if m2:
                        c2 = text[max(0, m2.start() - 5) : m2.end() + 5]
                        print(f"  Found variant '{alt}' in {ref}: ...{c2}...")
                        break
                else:
                    # Show first 100 chars
                    print(f"  No match for '{cand}' in snippet: {text[:80]}...")
    else:
        print(f"  No results")
