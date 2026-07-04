import requests, re, os, time, json, urllib.parse

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"
HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
API_URL = "https://en.wikipedia.org/w/api.php"

slugs = [
    (999, "b-lloyd-poelman", "B. Lloyd Poelman"),
    (1704, "christian-whitmer", "Christian Whitmer"),
    (1063, "clarissa-a-beesley", "Clarissa A. Beesley"),
    (1073, "colleen-b-lemmon", "Colleen B. Lemmon"),
    (1091, "david-a-smith", "David A. Smith"),
    (1112, "dessie-grant-boyle", "Dessie Grant Boyle"),
    (1119, "donna-d-sorensen", "Donna D. Sorensen"),
    (1120, "dorothy-p-holt", "Dorothy P. Holt"),
    (1121, "dorthea-c-murdock", "Dorthea C. Murdock"),
    (1131, "edith-hunter-lambert", "Edith Hunter Lambert"),
    (1138, "edward-hunter", "Edward Hunter"),
    (1141, "eileen-r-dunyon", "Eileen R. Dunyon"),
    (1145, "elbert-r-curtis", "Elbert R. Curtis"),
    (1168, "florence-r-lane", "Florence R. Lane"),
    (1176, "g-carlos-smith", "G. Carlos Smith"),
    (1187, "george-goddard", "George Goddard"),
    (1189, "george-miller", "George Miller"),
    (1193, "george-r-hill", "George R. Hill"),
    (1195, "george-reynolds", "George Reynolds"),
    (1221, "helen-s-williams", "Helen S. Williams"),
    (1227, "henry-harriman", "Henry Harriman"),
    (1708, "hiram-page", "Hiram Page"),
    (1230, "hortense-h-c-smith", "Hortense H. C. Smith"),
    (1247, "j-ballard-washburn", "J. Ballard Washburn"),
    (1250, "j-hugh-baird", "J. Hugh Baird"),
    (1251, "j-kent-jolley", "J. Kent Jolley"),
    (1254, "j-richard-clarke", "J. Richard Clarke"),
    (1266, "james-foster", "James Foster"),
    (1267, "james-h-hart", "James H. Hart"),
    (1270, "james-m-paramore", "James M. Paramore"),
    (1281, "jayne-b-malan", "Jayne B. Malan"),
    (1291, "jesse-gause", "Jesse Gause"),
    (1292, "joanne-b-doxey", "Joanne B. Doxey"),
    (1300, "john-corrill", "John Corrill"),
]


def api_call(params):
    try:
        r = requests.get(API_URL, params=params, headers=HEADERS, timeout=15)
        return r.json() if r.status_code == 200 else None
    except:
        return None


def search_wiki(title):
    params = {
        "action": "query",
        "format": "json",
        "titles": title,
        "prop": "pageimages",
        "pithumbsize": 800,
        "piprop": "original|name",
    }
    data = api_call(params)
    if not data:
        return None
    pages = data.get("query", {}).get("pages", {})
    for pid, info in pages.items():
        if pid == "-1":
            continue
        if "original" in info:
            return info["original"]["source"]
        if "thumbnail" in info:
            return info["thumbnail"]["source"]
    return None


results = {"found": [], "not_found": []}

for pid, slug, title in slugs:
    existing = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(existing) and os.path.getsize(existing) > 1000:
        print(f"  SKIP {slug} (exists)")
        continue

    print(f"  {title:35s}...", end=" ", flush=True)

    # Try different search titles
    found = None
    variants = [
        title,
        title.replace(". ", " "),
        title + " (Mormon)",
        title + " (Latter-day Saint)",
    ]
    for vt in variants:
        found = search_wiki(vt)
        if found:
            break

    if found:
        # Download the image
        try:
            r = requests.get(found, headers=HEADERS, timeout=20)
            if r.status_code == 200 and len(r.content) > 1000:
                path = os.path.join(UPLOADS, f"{slug}.jpg")
                with open(path, "wb") as f:
                    f.write(r.content)
                print(f"FOUND ({len(r.content)} bytes)")
                results["found"].append((pid, slug, title))
            else:
                print(f"found but download failed ({len(r.content)} bytes)")
                results["not_found"].append((pid, slug, "download_failed"))
        except:
            print("download error")
            results["not_found"].append((pid, slug, "download_error"))
    else:
        print("not found")
        results["not_found"].append((pid, slug, "no_wiki_page"))

    time.sleep(0.3)

print("\n\n=== RESULTS ===")
print(f"\nFOUND ({len(results['found'])}):")
for pid, slug, t in results["found"]:
    sz = (
        os.path.getsize(os.path.join(UPLOADS, f"{slug}.jpg"))
        if os.path.exists(os.path.join(UPLOADS, f"{slug}.jpg"))
        else 0
    )
    print(f"  {pid:5d}  {slug:35s}  {t} ({sz} bytes)")

print(f"\nNOT FOUND ({len(results['not_found'])}):")
for pid, slug, reason in results["not_found"]:
    print(f"  {pid:5d}  {slug:35s}  ({reason})")
