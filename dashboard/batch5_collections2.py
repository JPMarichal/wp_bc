import requests, urllib3, re, json, os

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"
session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

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


# Existing downloaded images (to skip)
def get_existing():
    existing = set()
    for f in os.listdir(UPLOADS):
        if f.endswith(".jpg") and not any(
            x in f
            for x in ["-150x", "-160x", "-300x", "-200x", "-240x", "-225x", "-237x"]
        ):
            existing.add(f.replace(".jpg", ""))
    return existing


existing = get_existing()

# All 34 missing + spencer-w-kimball for redownload
target_slugs = [
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


# Build string matching for each target
def extract_lastname(slug):
    return slug.split("-")[-1]


def extract_firstname(slug):
    parts = slug.split("-")
    for p in parts:
        if len(p) > 1:
            return p
    return parts[0]


def clean_name(n):
    return n.replace("\\u0026", "&").replace("\\/", "/").replace("\u0026", "&").strip()


all_items = []

for col in collections:
    url = f"https://www.churchofjesuschrist.org/media/collection/{col}?lang=eng"
    print(f"Scanning {col}...")
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

            # Extract ALL image URLs with their hash
            # Church image URLs: /imgs/{hash}/full/{size}/0/default
            imgs = re.findall(
                "(https://www\\.churchofjesuschrist\\.org/imgs/([a-f0-9]+)[^\"\\\\' <>]+)",
                dec,
            )

            # Extract ALL name-like strings near images
            names_near_imgs = re.findall(
                r'(?:name|title|altText)\\":\\\"([^\\\"]+)', dec
            )

            for img_url, img_hash in set(imgs):
                full_img = re.sub(r"/full/[^/]+/", "/full/!1280,1600/", img_url)
                all_items.append((full_img, names_near_imgs))

        print(f"  Found {len(all_items)} total items so far")
    except Exception as e:
        print(f"  ERROR: {e}")

print(f"\nTotal items collected: {len(all_items)}")

# Try to match by downloading and checking names
# Better approach: just get ALL unique images and try to match
# First, deduplicate by hash
seen_hashes = set()
unique_imgs = []
for img_url, names in all_items:
    h = re.search(r"/imgs/([a-f0-9]+)", img_url)
    h_val = h.group(1) if h else img_url
    if h_val not in seen_hashes:
        seen_hashes.add(h_val)
        unique_imgs.append(img_url)

print(f"Unique images: {len(unique_imgs)}")

# Now, since we can't easily match names, let's try a different strategy:
# Check if any of these image names match our slugs via simple substring matching
# Let's look at ALL names extracted
print("\nSample names found in RSC payload:")
for col in collections[:3]:
    url = f"https://www.churchofjesuschrist.org/media/collection/{col}?lang=eng"
    r = session.get(url, timeout=15)
    chunks = re.findall(
        r"self\.__next_f\.push\(\[([0-9]+),\"(.*?)\"\]\);?", r.text, re.DOTALL
    )
    names_found = set()
    for idx, chunk in chunks:
        try:
            dec = chunk.encode().decode("unicode_escape")
        except:
            dec = chunk
        names = re.findall(r"(?:name|title|altText)\":\"([^\"]+)", dec)
        for n in names:
            c = clean_name(n)
            if len(c) > 3 and len(c) < 100:
                names_found.add(c)
    print(f"  {col}: {len(names_found)} names")
    # Show names that might match our targets
    for n in sorted(names_found):
        for slug in target_slugs:
            last = extract_lastname(slug)
            if last.lower() in n.lower() and len(n) < 80:
                if slug not in existing:
                    print(f"    {slug} -> {n}")
                    break
    break
