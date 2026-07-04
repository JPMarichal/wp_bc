import requests, os, re, time, urllib3, json

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

people = [
    (999, "b-lloyd-poelman", "B. Lloyd Poelman"),
    (1029, "burton-k-farnsworth", "Burton K. Farnsworth"),
    (1063, "clarissa-a-beesley", "Clarissa A. Beesley"),
    (1069, "clement-m-matswagothata", "Clement M. Matswagothata"),
    (1073, "colleen-b-lemmon", "Colleen B. Lemmon"),
    (1087, "daniel-s-miles", "Daniel S. Miles"),
    (1091, "david-a-smith", "David A. Smith"),
    (1097, "david-lawrence-mckay", "David Lawrence McKay"),
    (1112, "dessie-grant-boyle", "Dessie Grant Boyle"),
    (1119, "donna-d-sorensen", "Donna D. Sorensen"),
    (1120, "dorothy-p-holt", "Dorothy P. Holt"),
    (1121, "dorthea-c-murdock", "Dorthea C. Murdock"),
    (1131, "edith-hunter-lambert", "Edith Hunter Lambert"),
    (1138, "edward-hunter", "Edward Hunter"),
    (1141, "eileen-r-dunyon", "Eileen R. Dunyon"),
    (1145, "elbert-r-curtis", "Elbert R. Curtis"),
    (1152, "emily-h-bennett", "Emily H. Bennett"),
    (1167, "florence-h-richards", "Florence H. Richards"),
    (1168, "florence-r-lane", "Florence R. Lane"),
    (1176, "g-carlos-smith", "G. Carlos Smith"),
    (1187, "george-goddard", "George Goddard"),
    (1189, "george-miller", "George Miller"),
    (1193, "george-r-hill", "George R. Hill"),
    (1195, "george-reynolds", "George Reynolds"),
    (1201, "glen-l-pace", "Glen L. Pace"),
    (1221, "helen-s-williams", "Helen S. Williams"),
    (1227, "henry-harriman", "Henry Harriman"),
    (1708, "hiram-page", "Hiram Page"),
    (1230, "hortense-h-c-smith", "Hortense H. C. Smith"),
    (1245, "isabelle-salmon-ross", "Isabelle Salmon Ross"),
    (1247, "j-ballard-washburn", "J. Ballard Washburn"),
    (1248, "j-devn-cornish", "J. Devn Cornish"),
    (1250, "j-hugh-baird", "J. Hugh Baird"),
    (1251, "j-kent-jolley", "J. Kent Jolley"),
    (1252, "j-kimo-esplin", "J. Kimo Esplin"),
    (1254, "j-richard-clarke", "J. Richard Clarke"),
    (1263, "james-e-evanson", "James E. Evanson"),
    (1266, "james-foster", "James Foster"),
    (1267, "james-h-hart", "James H. Hart"),
    (1270, "james-m-paramore", "James M. Paramore"),
    (1275, "jan-e-newman", "Jan E. Newman"),
    (1278, "janet-murdock-thompson", "Janet Murdock Thompson"),
    (1279, "janette-hales-beckham", "Janette Hales Beckham"),
    (1281, "jayne-b-malan", "Jayne B. Malan"),
    (1287, "jerald-l-taylor", "Jerald L. Taylor"),
    (1289, "jeremy-r-jaggi", "Jeremy R. Jaggi"),
    (1291, "jesse-gause", "Jesse Gause"),
    (1292, "joanne-b-doxey", "Joanne B. Doxey"),
    (1300, "john-corrill", "John Corrill"),
]

session = requests.Session()
session.verify = False
session.headers.update({
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
})

WIKI_API = "https://en.wikipedia.org/w/api.php"

def search_wikipedia(title_query):
    params = {
        "action": "query",
        "format": "json",
        "list": "search",
        "srsearch": title_query + " Latter-day Saint",
        "srlimit": 3,
        "srprop": "",
    }
    r = session.get(WIKI_API, params=params, timeout=15)
    if r.status_code == 200:
        data = r.json()
        pages = data.get("query", {}).get("search", [])
        if pages:
            return pages[0]["title"]
    return None

def get_wiki_image(page_title):
    params = {
        "action": "query",
        "format": "json",
        "titles": page_title,
        "prop": "pageimages",
        "pithumbsize": 800,
        "piprop": "original|name",
    }
    r = session.get(WIKI_API, params=params, timeout=15)
    if r.status_code == 200:
        data = r.json()
        pages = data.get("query", {}).get("pages", {})
        for pid, info in pages.items():
            if "original" in info:
                return info["original"]["source"]
            elif "thumbnail" in info:
                return info["thumbnail"]["source"]
    return None

def download_image(url, filepath):
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

for pid, slug, title in people:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path):
        print(f"EXISTS {slug}")
        found += 1
        continue

    # Build search names
    parts = slug.split("-")
    # Get first real name (skip single-letter initials)
    first = ""
    last = parts[-1]
    for p in parts:
        if len(p) > 1:
            first = p
            break
    if not first:
        first = parts[0]

    search_queries = [
        f"{title}",
        f"{first} {last}",
        title.replace(". ", " "),
    ]

    img_url = None
    for q in search_queries:
        wiki_title = search_wikipedia(q + " Mormon")
        if not wiki_title:
            wiki_title = search_wikipedia(q)
        if wiki_title:
            img_url = get_wiki_image(wiki_title)
            if img_url:
                break

    if img_url:
        size = download_image(img_url, jpg_path)
        if size:
            print(f"OK     {slug} ({size} bytes) from Wikipedia")
            found += 1
        else:
            print(f"FAILDL {slug}")
            not_found += 1
    else:
        # Try churchofjesuschrist.org media library CDN directly
        cdn_url = f"https://www.churchofjesuschrist.org/imgs/{slug}/"
        try:
            r = session.get(cdn_url, timeout=10)
            if r.status_code == 200 and "img" in r.text.lower():
                print(f"CDN?   {slug}")
                # Extract image URL
                imgs = re.findall(r'https://[^"\\']+\.(?:jpg|jpeg|png)', r.text)
                for im in imgs:
                    size = download_image(im, jpg_path)
                    if size:
                        print(f"OK_CDN {slug} ({size} bytes)")
                        found += 1
                        break
                else:
                    print(f"NOCDN  {slug}")
                    not_found += 1
            else:
                print(f"NO_WIKI {slug}")
                not_found += 1
        except:
            print(f"NO_WIKI {slug}")
            not_found += 1

    time.sleep(0.3)

print(f"\nDone! Found: {found}, Not found: {not_found}")
