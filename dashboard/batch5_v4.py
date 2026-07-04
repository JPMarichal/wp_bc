import requests, os, re, time, urllib3, json, sys

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

# Remaining after first pass (41 people)
remaining = [
    (999, "b-lloyd-poelman", "B. Lloyd Poelman"),
    (1029, "burton-k-farnsworth", "Burton K. Farnsworth"),
    (1704, "christian-whitmer", "Christian Whitmer"),
    (1063, "clarissa-a-beesley", "Clarissa A. Beesley"),
    (1073, "colleen-b-lemmon", "Colleen B. Lemmon"),
    (1087, "daniel-s-miles", "Daniel S. Miles"),
    (1091, "david-a-smith", "David A. Smith"),
    (1112, "dessie-grant-boyle", "Dessie Grant Boyle"),
    (1119, "donna-d-sorensen", "Donna D. Sorensen"),
    (1120, "dorothy-p-holt", "Dorothy P. Holt"),
    (1121, "dorthea-c-murdock", "Dorthea C. Murdock"),
    (1131, "edith-hunter-lambert", "Edith Hunter Lambert"),
    (1138, "edward-hunter", "Edward Hunter"),
    (1141, "eileen-r-dunyon", "Eileen R. Dunyon"),
    (1145, "elbert-r-curtis", "Elbert R. Curtis"),
    (1167, "florence-h-richards", "Florence H. Richards"),
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
    (1245, "isabelle-salmon-ross", "Isabelle Salmon Ross"),
    (1247, "j-ballard-washburn", "J. Ballard Washburn"),
    (1248, "j-devn-cornish", "J. Devn Cornish"),
    (1250, "j-hugh-baird", "J. Hugh Baird"),
    (1251, "j-kent-jolley", "J. Kent Jolley"),
    (1254, "j-richard-clarke", "J. Richard Clarke"),
    (1266, "james-foster", "James Foster"),
    (1267, "james-h-hart", "James H. Hart"),
    (1270, "james-m-paramore", "James M. Paramore"),
    (1278, "janet-murdock-thompson", "Janet Murdock Thompson"),
    (1279, "janette-hales-beckham", "Janette Hales Beckham"),
    (1281, "jayne-b-malan", "Jayne B. Malan"),
    (1291, "jesse-gause", "Jesse Gause"),
    (1292, "joanne-b-doxey", "Joanne B. Doxey"),
    (1300, "john-corrill", "John Corrill"),
]

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

WIKI_API = "https://en.wikipedia.org/w/api.php"
COMMONS_API = "https://commons.wikimedia.org/w/api.php"


def get_wikipedia_image(page_title):
    """Get the main image for a Wikipedia page"""
    params = {
        "action": "query",
        "format": "json",
        "titles": page_title,
        "prop": "pageimages",
        "pithumbsize": 800,
        "piprop": "original|name",
    }
    r = session.get(WIKI_API, params=params, timeout=10)
    if r.status_code == 200:
        data = r.json()
        for pid, info in data.get("query", {}).get("pages", {}).items():
            if "original" in info:
                return info["original"]["source"]
    return None


def search_and_get_image(query, extra=""):
    """Search Wikipedia and get the first image found"""
    search_query = query + extra
    params = {
        "action": "query",
        "format": "json",
        "list": "search",
        "srsearch": search_query,
        "srlimit": 5,
        "srprop": "",
    }
    r = session.get(WIKI_API, params=params, timeout=10)
    if r.status_code != 200:
        return None
    data = r.json()
    for page in data.get("query", {}).get("search", []):
        p_title = page["title"]
        img = get_wikipedia_image(p_title)
        if img:
            return img
    return None


def try_church_media_library(lastname):
    """Try the church media library image CDN format"""
    url = f"https://www.churchofjesuschrist.org/imgs/{lastname}"
    try:
        r = session.get(url, timeout=10)
        if r.status_code == 200 and len(r.text) > 100:
            imgs = re.findall(r'https://[^"\'\\ ]+\.(?:jpg|jpeg|png)[^"\' <>]*', r.text)
            for im in imgs:
                return im
    except:
        pass
    return None


def download(url, filepath):
    try:
        r = session.get(url, timeout=30)
        if r.status_code == 200 and len(r.content) > 2000:
            with open(filepath, "wb") as f:
                f.write(r.content)
            return len(r.content)
    except:
        pass
    return None


found = 0
not_found = 0

for pid, slug, title in remaining:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path):
        print(f"EXISTS {slug}")
        found += 1
        continue

    parts = slug.split("-")
    first_real = ""
    for p in parts:
        if len(p) > 1:
            first_real = p
            break
    last = parts[-1]

    # Clean title for search
    clean_title = title.replace(". ", " ").replace(".", " ").strip()

    img_url = None

    # Strategy 1: Search Wikipedia with "Latter-day Saint" qualifier
    img_url = search_and_get_image(clean_title, " Latter-day Saint")

    # Strategy 2: Try without qualifier
    if not img_url:
        img_url = search_and_get_image(clean_title, "")

    # Strategy 3: Try with "Mormon" qualifier
    if not img_url:
        img_url = search_and_get_image(clean_title, " Mormon")

    # Strategy 4: Try first name + last name
    if not img_url and first_real:
        img_url = search_and_get_image(f"{first_real} {last}", " Latter-day Saint")

    # Strategy 5: Try church media library
    if not img_url:
        img_url = try_church_media_library(last)

    if img_url:
        size = download(img_url, jpg_path)
        if size:
            print(f"OK     {slug} ({size} bytes)")
            found += 1
        else:
            print(f"FAILDL {slug}")
            not_found += 1
    else:
        print(f"NOIMG  {slug}")
        not_found += 1

    time.sleep(0.3)

print(f"\nDone! Found: {found}, Not found: {not_found}")
