import requests, os, re, time, urllib3
from bs4 import BeautifulSoup

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

# First 50 people without thumbnails
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

found = 0
not_found = 0

for pid, slug, title in people:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path):
        print(f"EXISTS {slug}")
        found += 1
        continue

    url = f"https://www.churchofjesuschrist.org/study/general-conference/speaker/{slug}?lang=eng"

    try:
        r = session.get(url, timeout=15)
        if r.status_code != 200:
            # Try alternate URL format
            url2 = f"https://www.churchofjesuschrist.org/study/general-conference/speaker/{slug.replace('-', '')}?lang=eng"
            r = session.get(url2, timeout=15)

        if r.status_code != 200:
            print(f"NOPAGE {slug}")
            not_found += 1
            continue

        soup = BeautifulSoup(r.text, "html.parser")

        talk_link = None
        for a in soup.select("a[href*='/study/general-conference/']"):
            href = a.get("href", "")
            if "/speaker/" not in href and "/study/general-conference/" in href:
                talk_link = href
                break

        if not talk_link:
            print(f"NOTALK {slug}")
            not_found += 1
            continue

        if talk_link.startswith("/"):
            talk_link = "https://www.churchofjesuschrist.org" + talk_link

        tr = session.get(talk_link, timeout=15)
        tsoup = BeautifulSoup(tr.text, "html.parser")

        og_image = None
        for meta in tsoup.select("meta[property='og:image']"):
            og_image = meta.get("content")
            break

        if not og_image:
            for meta in tsoup.select("meta[name='twitter:image']"):
                og_image = meta.get("content")
                break

        if not og_image:
            print(f"NOIMAGE {slug}")
            not_found += 1
            continue

        # Get full size image
        img_url = og_image.rsplit("?", 1)[0] if "?" in og_image else og_image
        img_url = re.sub(r"(!\d+,\d+)?$", "!1280,1600", img_url.split("?")[0])

        img_r = session.get(img_url, timeout=30)
        if img_r.status_code == 200:
            with open(jpg_path, "wb") as f:
                f.write(img_r.content)
            print(f"OK     {slug} ({len(img_r.content)} bytes)")
            found += 1
        else:
            # Fallback: try the og:image URL as-is
            img_r = session.get(og_image, timeout=30)
            if img_r.status_code == 200:
                with open(jpg_path, "wb") as f:
                    f.write(img_r.content)
                print(f"OK_FB  {slug} ({len(img_r.content)} bytes)")
                found += 1
            else:
                print(f"FAILDL {slug}: HTTP {img_r.status_code}")
                not_found += 1

        time.sleep(0.5)

    except Exception as e:
        print(f"ERROR  {slug}: {e}")
        not_found += 1

print(f"\nDone! Found: {found}, Not found: {not_found}")
