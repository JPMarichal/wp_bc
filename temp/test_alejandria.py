import json, urllib.request, sys


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
        ).encode("utf-8"),
        headers={
            "Content-Type": "application/json",
            "Accept": "application/json, text/event-stream",
        },
    )
    resp = urllib.request.urlopen(req)
    body = resp.read().decode("utf-8")
    for line in body.split("\n"):
        if line.startswith("data: "):
            d = json.loads(line[6:])
            return json.loads(d["result"]["content"][0]["text"])
    return None


# Test: search for Jericó in RV60
result = mcp_call(
    "search_text", {"query": "Jericó", "source_filter": "es/biblia", "limit": 5}
)
if result:
    print(f"Query: {result.get('query')}")
    print(f"Count: {result.get('count')}")
    for r in result.get("results", []):
        print(f"  Score={r['score']:.3f} | Ref={r.get('reference', '')}")
        print(f"  Text: {r['text'][:100]}")
else:
    print("No results or error")
    print(json.dumps(result, indent=2) if result else "None")
