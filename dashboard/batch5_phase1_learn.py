import requests, re, os, time, sys

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
    "Accept-Language": "en-US,en;q=0.5",
}

slugs = [
    (999, "b-lloyd-poelman"),
    (1704, "christian-whitmer"),
    (1063, "clarissa-a-beesley"),
    (1073, "colleen-b-lemmon"),
    (1091, "david-a-smith"),
    (1112, "dessie-grant-boyle"),
    (1119, "donna-d-sorensen"),
    (1120, "dorothy-p-holt"),
    (1121, "dorthea-c-murdock"),
    (1131, "edith-hunter-lambert"),
    (1138, "edward-hunter"),
    (1141, "eileen-r-dunyon"),
    (1145, "elbert-r-curtis"),
    (1168, "florence-r-lane"),
    (1176, "g-carlos-smith"),
    (1187, "george-goddard"),
    (1189, "george-miller"),
    (1193, "george-r-hill"),
    (1195, "george-reynolds"),
    (1221, "helen-s-williams"),
    (1227, "henry-harriman"),
    (1708, "hiram-page"),
    (1230, "hortense-h-c-smith"),
    (1247, "j-ballard-washburn"),
    (1250, "j-hugh-baird"),
    (1251, "j-kent-jolley"),
    (1254, "j-richard-clarke"),
    (1266, "james-foster"),
    (1267, "james-h-hart"),
    (1270, "james-m-paramore"),
    (1281, "jayne-b-malan"),
    (1291, "jesse-gause"),
    (1292, "joanne-b-doxey"),
    (1300, "john-corrill"),
]


def try_fetch(url, slug):
    try:
        r = requests.get(url, headers=HEADERS, timeout=20, verify=False)
        if r.status_code != 200:
            return None
        # Try to find og:image
        m = re.search(
            r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.IGNORECASE
        )
        if m:
            return m.group(1)
        m = re.search(
            r'<meta\s+content="([^"]+)"\s+property="og:image"', r.text, re.IGNORECASE
        )
        if m:
            return m.group(1)
        return None
    except Exception as e:
        return None


def download_image(url, slug):
    try:
        r = requests.get(url, headers=HEADERS, timeout=30, verify=False)
        if r.status_code == 200 and len(r.content) > 1000:
            path = os.path.join(UPLOADS, f"{slug}.jpg")
            with open(path, "wb") as f:
                f.write(r.content)
            return len(r.content)
        return 0
    except:
        return 0


results = {"found": [], "not_found": [], "error": []}

for pid, slug in slugs:
    existing = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(existing) and os.path.getsize(existing) > 1000:
        print(f"  SKIP {slug} (already exists, {os.path.getsize(existing)} bytes)")
        results["found"].append((pid, slug, "cached"))
        continue

    print(f"  Trying {slug}...", end=" ", flush=True)

    # Try /learn/people/{slug} first (canonical format)
    url1 = f"https://www.churchofjesuschrist.org/learn/people/{slug}?lang=eng"
    og_url = try_fetch(url1, slug)
    if not og_url:
        # Fallback: /learn/{slug}
        url2 = f"https://www.churchofjesuschrist.org/learn/{slug}?lang=eng"
        og_url = try_fetch(url2, slug)

    if og_url:
        size = download_image(og_url, slug)
        if size > 1000:
            print(f"FOUND og:image ({size} bytes)")
            results["found"].append((pid, slug, og_url))
        else:
            print(f"og:image found but download failed ({size} bytes)")
            results["not_found"].append((pid, slug, "download_failed"))
    else:
        print("not found")
        results["not_found"].append((pid, slug, "no_learn_page"))

    time.sleep(0.5)  # Rate limiting

print("\n\n=== RESULTS ===")
print(f"\nFOUND ({len(results['found'])}):")
for pid, slug, url in results["found"]:
    print(f"  {pid:5d}  {slug:35s}  {url}")

print(f"\nNOT FOUND ({len(results['not_found'])}):")
for pid, slug, reason in results["not_found"]:
    print(f"  {pid:5d}  {slug:35s}  ({reason})")
