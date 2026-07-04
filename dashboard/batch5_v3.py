import requests, os, re, time, urllib3, json

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

people = [
    (999, "b-lloyd-poelman", "B. Lloyd Poelman"),
    (1029, "burton-k-farnsworth", "Burton K. Farnsworth"),
    (1704, "christian-whitmer", "Christian Whitmer"),
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
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

WIKI_API = "https://en.wikipedia.org/w/api.php"

# Step 1: Build a map of known talk pages from recent conferences
print("Building speaker map from recent conferences...")
conferences_to_check = [
    "2026/04",
    "2025/10",
    "2025/04",
    "2024/10",
    "2024/04",
    "2023/10",
    "2023/04",
]

speaker_talk_map = {}  # lastname -> list of talk URLs

for conf in conferences_to_check:
    url = (
        f"https://www.churchofjesuschrist.org/study/general-conference/{conf}?lang=eng"
    )
    try:
        r = session.get(url, timeout=15)
        links = re.findall(
            r"href=\"(/study/general-conference/[^\"]+/[0-9]+[a-z]+)\?lang=eng\"",
            r.text,
        )
        for link in links:
            m = re.match(r"/study/general-conference/[^/]+/[^/]+/[0-9]+([a-z]+)", link)
            if m:
                last = m.group(1)
                full_url = "https://www.churchofjesuschrist.org" + link + "?lang=eng"
                if last not in speaker_talk_map:
                    speaker_talk_map[last] = []
                speaker_talk_map[last].append(full_url)
        print(f"  {conf}: {len(links)} talks found")
    except Exception as e:
        print(f"  {conf}: ERROR {e}")
    time.sleep(0.5)

print(f"Total speakers in map: {len(speaker_talk_map)}")

# Step 2: For each person, try to get an image
found = 0
not_found = 0

for pid, slug, title in people:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path):
        print(f"EXISTS {slug}")
        found += 1
        continue

    # Get the last name from slug
    parts = slug.split("-")
    lastname = parts[-1]

    # Also try without the number prefix
    alt_lastnames = set()
    alt_lastnames.add(lastname)
    # For names like "b-lloyd-poelman", the real last name is "poelman"
    # For "j-ballard-washburn", it's "washburn"
    # For "g-carlos-smith", it's "smith"
    # Etc.

    img_url = None

    # Try to find from our speaker map
    talk_urls = speaker_talk_map.get(lastname, [])
    if not talk_urls:
        # Try alt last name: the last non-initial part before the end
        # For slugs like "j-devn-cornish", try "devn-cornish" too
        # For "clarissa-a-beesley", try just "beesley"
        pass

    for talk_url in talk_urls:
        try:
            tr = session.get(talk_url, timeout=15)
            og = re.search(
                r'<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"]',
                tr.text,
            )
            if og:
                img_url = og.group(1)
                break
        except:
            pass

    if not img_url:
        # Try Wikipedia for this person
        search_terms = [
            title,
            title.replace(". ", " "),
        ]
        for term in search_terms:
            params = {
                "action": "query",
                "format": "json",
                "list": "search",
                "srsearch": term + " Latter-day Saint",
                "srlimit": 3,
            }
            try:
                r = session.get(WIKI_API, params=params, timeout=10)
                if r.status_code == 200:
                    data = r.json()
                    pages = data.get("query", {}).get("search", [])
                    for p in pages:
                        p_title = p["title"]
                        params2 = {
                            "action": "query",
                            "format": "json",
                            "titles": p_title,
                            "prop": "pageimages",
                            "pithumbsize": 800,
                            "piprop": "original|name",
                        }
                        r2 = session.get(WIKI_API, params=params2, timeout=10)
                        if r2.status_code == 200:
                            d2 = r2.json()
                            for pid2, info in (
                                d2.get("query", {}).get("pages", {}).items()
                            ):
                                if "original" in info:
                                    img_url = info["original"]["source"]
                                    break
                                elif "thumbnail" in info:
                                    img_url = info["thumbnail"]["source"]
                                    break
                    if img_url:
                        break
            except:
                pass

    if img_url:
        # Download
        try:
            size_url = re.sub(r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", img_url)
            r = session.get(size_url, timeout=30)
            if r.status_code != 200 or len(r.content) < 1000:
                r = session.get(img_url, timeout=30)

            if r.status_code == 200 and len(r.content) > 1000:
                with open(jpg_path, "wb") as f:
                    f.write(r.content)
                print(f"OK     {slug} ({len(r.content)} bytes)")
                found += 1
            else:
                print(f"FAILDL {slug}")
                not_found += 1
        except Exception as e:
            print(f"ERRDL  {slug}: {e}")
            not_found += 1
    else:
        print(f"NOIMG  {slug}")
        not_found += 1

    time.sleep(0.3)

print(f"\nDone! Found: {found}, Not found: {not_found}")
