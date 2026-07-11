"""Debug: check actual MCP response structure."""

import json, urllib.request


def mcp_debug(method, params):
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
    resp = urllib.request.urlopen(req, timeout=10)
    body = resp.read().decode("utf-8")

    lines = body.split(chr(10))
    for line in lines:
        if line.startswith("data: "):
            d = json.loads(line[6:])
            print("Top-level keys:", list(d.keys()))
            if "result" in d:
                print("Result keys:", list(d["result"].keys()))
                if "content" in d["result"]:
                    content = d["result"]["content"]
                    print(f"Content type: {type(content)}, len: {len(content)}")
                    if content:
                        item = content[0]
                        print(
                            f"Content[0] keys: {list(item.keys()) if isinstance(item, dict) else 'not a dict'}"
                        )
                        print(
                            f"Content[0]: {json.dumps(item, ensure_ascii=False)[:300]}"
                        )
            print()
            print("Full response (first 500 chars):", body[:500])


print("=== Search English: Ramah ===")
mcp_debug(
    "search_text", {"query": "Ramah", "limit": 1, "source_filter": "en/scriptures"}
)
