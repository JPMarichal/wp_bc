import requests, urllib3, re, json, os

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)


def extract_items_from_collection(url):
    """Extract image+name pairs from a media collection"""
    r = session.get(url, timeout=15)
    chunks = re.findall(
        r"self\.__next_f\.push\(\[([0-9]+),\"(.*?)\"\]\);?", r.text, re.DOTALL
    )

    items = []  # [(name, image_url), ...]

    for idx, chunk in chunks:
        try:
            dec = chunk.encode().decode("unicode_escape")
        except:
            dec = chunk

        # Extract name fields (from altText, name, title)
        names = re.findall(r"\"altText\":\"([^\"]*?)\"", dec)

        # Extract image URLs (original size - /!1280, or /!320, thumbnails)
        # The full-res version typically has /!1280, in the path
        image_urls_1280 = re.findall(
            r"\"src\":\"(https://www\.churchofjesuschrist\.org/imgs/[a-f0-9]+/full/!1280,[^\"<>]+)",
            dec,
        )
        image_urls_200 = re.findall(
            r"\"src\":\"(https://www\.churchofjesuschrist\.org/imgs/[a-f0-9]+/full/,200[^\"<>]+)",
            dec,
        )

        # If we have both names and images in same chunk, pair them
        if names and (image_urls_1280 or image_urls_200):
            imgs = image_urls_1280 if image_urls_1280 else image_urls_200
            # Sometimes names and images arrays might be different lengths
            # Use the shorter one to avoid index errors
            min_len = min(len(names), len(imgs))
            for i in range(min_len):
                name = names[i].replace("\\u0026", "&").replace("\\/", "/")
                img = imgs[i]
                items.append((name, img))

    return items


# Collections to scan
collections = [
    (
        "general-officers",
        "https://www.churchofjesuschrist.org/media/collection/general-officers?lang=eng",
    ),
    (
        "presiding-bishopric",
        "https://www.churchofjesuschrist.org/media/collection/presiding-bishopric-and-presidency-of-the-seventy-images?lang=eng",
    ),
    (
        "first-presidency-q12",
        "https://www.churchofjesuschrist.org/media/collection/first-presidency-and-quorum-of-the-twelve-apostles-images?lang=eng",
    ),
    (
        "yw-presidents",
        "https://www.churchofjesuschrist.org/media/collection/young-women-general-presidents-images?lang=eng",
    ),
    (
        "rs-presidents",
        "https://www.churchofjesuschrist.org/media/collection/relief-society-general-presidents-images?lang=eng",
    ),
    (
        "primary-presidents",
        "https://www.churchofjesuschrist.org/media/collection/primary-general-presidents-images?lang=eng",
    ),
    (
        "church-presidents",
        "https://www.churchofjesuschrist.org/media/collection/presidents-of-the-church-of-jesus-christ-of-latter-day-saints-images?lang=eng",
    ),
    (
        "joseph-smith",
        "https://www.churchofjesuschrist.org/media/collection/joseph-smith-images?lang=eng",
    ),
]

all_items = {}
for col_name, col_url in collections:
    print(f"Scanning {col_name}...")
    items = extract_items_from_collection(col_url)
    print(f"  Found {len(items)} items")
    all_items[col_name] = items

# Check for Spencer W. Kimball
for col_name, items in all_items.items():
    for name, img in items:
        if "kimball" in name.lower():
            print(f"KIMBALL in {col_name}: {name[:80]} -> {img[:80]}")

print("\n\nAll items by collection:")
for col_name, items in all_items.items():
    print(f"\n=== {col_name} ({len(items)} items) ===")
    for name, img in items[:10]:
        print(f"  {name[:60]}")
