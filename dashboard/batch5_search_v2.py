import requests, os, re, time, urllib3, json
from bs4 import BeautifulSoup

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


def extract_last_name(slug):
    parts = slug.split("-")
    return parts[-1]


def try_urls(urls, name):
    for url in urls:
        try:
            r = session.get(url, timeout=15)
            if r.status_code == 200:
                return r
        except:
            pass
    return None


def extract_image_from_page(r):
    soup = BeautifulSoup(r.text, "html.parser")
    # Try OG image
    for meta in soup.select("meta[property='og:image'], meta[name='twitter:image']"):
        content = meta.get("content", "")
        if content:
            return content
    return None


def search_google_images(name):
    """Fallback: try to find image via Google image search"""
    q = f'"{name}" LDS Mormon churchofjesuschrist.org'
    url = f"https://www.google.com/search?tbm=isch&q={requests.utils.quote(q)}"
    try:
        r = session.get(url, timeout=15)
        # Look for image URLs in the response
        imgs = re.findall(r'src="(https://[^"]+)"', r.text)
        for img in imgs:
            if "churchofjesuschrist" in img or "lds" in img.lower():
                return img
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

    last = extract_last_name(slug)
    # Remove middle initial parts
    parts = slug.split("-")
    # Try: single-name, first-last, just-last
    first_name = parts[0] if parts else ""
    # Filter out single-letter initials for the first name
    name_variants = [slug]
    # Last name only
    name_variants.append(last)
    # Without middle initials
    simple = "-".join([p for p in parts if len(p) > 1])
    if simple != slug:
        name_variants.append(simple)

    # Build URLs to try
    urls_to_try = []
    for v in name_variants:
        urls_to_try.append(
            f"https://www.churchofjesuschrist.org/study/general-conference/speaker/{v}?lang=eng"
        )
    # Also try the /biography/ endpoint
    for v in name_variants:
        urls_to_try.append(
            f"https://www.churchofjesuschrist.org/study/manual/gospel-topics/{v}?lang=eng"
        )

    r = try_urls(urls_to_try, title)
    if r is None:
        print(f"NOPAGE {slug}")
        not_found += 1
        continue

    # Got a page, try to extract image
    og = extract_image_from_page(r)
    if og:
        # Download
        img_url = re.sub(r"(!\d+,\d+)?$", "!1280,1600", og.split("?")[0])
        try:
            img_r = session.get(img_url, timeout=30)
            if img_r.status_code == 200 and len(img_r.content) > 1000:
                with open(jpg_path, "wb") as f:
                    f.write(img_r.content)
                print(f"OK     {slug} ({len(img_r.content)} bytes)")
                found += 1
                time.sleep(0.5)
                continue
        except:
            pass

        # Fallback to original og url
        try:
            img_r = session.get(og, timeout=30)
            if img_r.status_code == 200 and len(img_r.content) > 1000:
                with open(jpg_path, "wb") as f:
                    f.write(img_r.content)
                print(f"OK_OG  {slug} ({len(img_r.content)} bytes)")
                found += 1
                time.sleep(0.5)
                continue
        except:
            pass

    # Try to find a talk link and get image from there
    soup = BeautifulSoup(r.text, "html.parser")
    for a in soup.select("a[href*='/study/general-conference/']"):
        href = a.get("href", "")
        if "/speaker/" not in href and "/study/general-conference/" in href:
            if href.startswith("/"):
                href = "https://www.churchofjesuschrist.org" + href
            try:
                tr = session.get(href, timeout=15)
                img = extract_image_from_page(tr)
                if img:
                    img_r = session.get(img, timeout=30)
                    if img_r.status_code == 200 and len(img_r.content) > 1000:
                        with open(jpg_path, "wb") as f:
                            f.write(img_r.content)
                        print(f"OK_TLK {slug} ({len(img_r.content)} bytes)")
                        found += 1
                        time.sleep(0.5)
                        break
            except:
                pass
    else:
        print(f"NOIMG  {slug}")
        not_found += 1

    time.sleep(0.5)

print(f"\nDone! Found: {found}, Not found: {not_found}")
