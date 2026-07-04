import requests, urllib3, re, json, os

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

# Collections to scan
collections = [
    "general-officers",
    "presiding-bishopric-and-presidency-of-the-seventy-images",
    "first-presidency-and-quorum-of-the-twelve-apostles-images",
    "young-women-general-presidents-images",
    "relief-society-general-presidents-images",
    "primary-general-presidents-images",
    "presidents-of-the-church-of-jesus-christ-of-latter-day-saints-images",
    "joseph-smith-images",
]


def extract_images_from_collection(url):
    """Extract all images with their names from a collection page"""
    r = session.get(url, timeout=15)
    chunks = re.findall(
        r"self\.__next_f\.push\(\[([0-9]+),\"(.*?)\"\]\);?", r.text, re.DOTALL
    )

    results = []
    for idx, chunk in chunks:
        try:
            dec = chunk.encode().decode("unicode_escape")
        except:
            dec = chunk

        # Find image items with surrounding context
        # Pattern: look for image URLs followed by name/title data
        img_pattern = r"\"(?:src|image)\"\s*:\s*\"(https://www\.churchofjesuschrist\.org/imgs/[^\"]+)\""
        imgs = re.findall(img_pattern, dec)

        # Find names in the surrounding data
        # Names might be in "title", "name", "altText" fields
        name_pattern = r"\"(?:title|name|altText)\"\s*:\s*\"([^\"]+)\""
        names = re.findall(name_pattern, dec)

        if imgs:
            # Filter out collection-level images and find individual leader images
            for img in imgs:
                # Skip thumbnails, get the full-res image
                full_img = re.sub(r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", img)
                full_img = re.sub(r"/full/[^/]+/", "/full/!1280,1600/", full_img)
                results.append(full_img)

    # Also look for items with image+name pairs in structured data
    items_pattern = r"\{\s*\"(?:image|thumbnail|photo)\"\s*:\s*\"([^\"]+)\"[^}]+\"(?:name|title)\"\s*:\s*\"([^\"]+)\""
    items = re.findall(items_pattern, r.text)
    for img, name in items:
        results.append((img, name))

    return results


def match_name_to_slug(name, target_slugs):
    """Match a name from the collection to our slug"""
    name_lower = name.lower().replace(".", "").replace(",", "")
    target_slugs_lower = {s: s.replace("-", " ") for s in target_slugs}

    for slug, normalized in target_slugs_lower.items():
        # Check if all significant words from slug appear in name
        slug_words = set(normalized.split())
        name_words = set(name_lower.split())

        # Last name must match
        last_name = slug.split("-")[-1]
        if last_name not in name_lower:
            continue

        # Check significant parts (skip initials)
        significant = [p for p in slug.split("-") if len(p) > 1]
        sig_matches = sum(1 for p in significant if p in name_lower)

        if sig_matches >= 2 or (sig_matches >= 1 and len(significant) == 1):
            return slug

    return None


# We're looking for all slug-based images
# Build lookup of already-existing jpgs
existing = set()
for f in os.listdir(UPLOADS):
    if f.endswith(".jpg") and not any(x in f for x in ["-150x", "-160x", "-300x"]):
        existing.add(f.replace(".jpg", ""))

# The 34 missing slugs
missing_slugs = [
    "b-lloyd-poelman",
    "christian-whitmer",
    "clarissa-a-beesley",
    "colleen-b-lemmon",
    "david-a-smith",
    "dessie-grant-boyle",
    "donna-d-sorensen",
    "dorothy-p-holt",
    "dorthea-c-murdock",
    "edith-hunter-lambert",
    "edward-hunter",
    "eileen-r-dunyon",
    "elbert-r-curtis",
    "florence-r-lane",
    "g-carlos-smith",
    "george-goddard",
    "george-miller",
    "george-r-hill",
    "george-reynolds",
    "helen-s-williams",
    "henry-harriman",
    "hiram-page",
    "hortense-h-c-smith",
    "j-ballard-washburn",
    "j-hugh-baird",
    "j-kent-jolley",
    "j-richard-clarke",
    "james-foster",
    "james-h-hart",
    "james-m-paramore",
    "jayne-b-malan",
    "jesse-gause",
    "joanne-b-doxey",
    "john-corrill",
]

# Also add spencer-w-kimball for re-download
missing_slugs.append("spencer-w-kimball")

print(
    f"Scanning {len(collections)} collections for {len(missing_slugs)} missing people + Spencer W. Kimball..."
)

all_images = {}

for col in collections:
    url = f"https://www.churchofjesuschrist.org/media/collection/{col}?lang=eng"
    print(f"  Scanning {col}...")

    try:
        r = session.get(url, timeout=15)
        chunks = re.findall(
            r"self\.__next_f\.push\(\[([0-9]+),\"(.*?)\"\]\);?", r.text, re.DOTALL
        )

        for idx, chunk in chunks:
            try:
                dec = chunk.encode().decode("unicode_escape")
            except:
                dec = chunk

            # Extract structured JSON objects from the RSC payload
            # Look for patterns like {"name":"...","src":"...","title":"..."}
            objs = re.findall(
                r"\{[^{}]*\"(?:name|title|altText)\"[^{}]*\"(?:src|image|thumbnail)\"[^{}]*\}|"
                + r"\{[^{}]*\"(?:src|image|thumbnail)\"[^{}]*\"(?:name|title|altText)\"[^{}]*\}",
                dec,
            )

            for obj in objs:
                names = re.findall(
                    r"\"(?:name|title|altText)\"\s*:\s*\"([^\"]+)\"", obj
                )
                imgs = re.findall(
                    r"\"(?:src|image|thumbnail)\"\s*:\s*\"(https://www\.churchofjesuschrist\.org/imgs/[^\"]+)\"",
                    obj,
                )

                if names and imgs:
                    for n in names:
                        n_clean = n.replace("\\u0026", "&").replace("\\/", "/")
                        slug_match = match_name_to_slug(n_clean, missing_slugs)
                        if slug_match and slug_match not in all_images:
                            img = re.sub(
                                r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", imgs[0]
                            )
                            img = re.sub(r"/full/[^/]+/", "/full/!1280,1600/", img)
                            all_images[slug_match] = (n_clean, img)
                            print(f"    FOUND {slug_match}: {n_clean}")

    except Exception as e:
        print(f"    ERROR: {e}")

print(f"\nFound {len(all_images)} images for missing people:")
for slug, (name, img_url) in sorted(all_images.items()):
    print(f"  {slug}: {name} -> {img_url[:80]}...")

# Download them
print("\nDownloading images...")
for slug, (name, img_url) in all_images.items():
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path):
        print(f"  EXISTS {slug}")
        continue

    try:
        r = session.get(img_url, timeout=30)
        if r.status_code == 200 and len(r.content) > 1000:
            with open(jpg_path, "wb") as f:
                f.write(r.content)
            print(f"  OK {slug} ({len(r.content)} bytes)")
        else:
            print(f"  FAIL {slug}: HTTP {r.status_code}, {len(r.content)} bytes")
    except Exception as e:
        print(f"  ERROR {slug}: {e}")
