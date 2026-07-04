import requests, time, os, json, re, sys, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

people = [
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
    (1302, "john-d-giles", "John D. Giles"),
    (1306, "john-gaylord", "John Gaylord"),
    (1309, "john-h-taylor", "John H. Taylor"),
    (1316, "john-morgan", "John Morgan"),
    (1318, "john-r-lasater", "John R. Lasater"),
    (1320, "john-s-tanner", "John S. Tanner"),
    (1321, "john-smith", "John Smith"),
    (1326, "john-wells", "John Wells"),
]


def wapi(params):
    try:
        r = session.get("https://en.wikipedia.org/w/api.php", params=params, timeout=10)
        if r.status_code == 429:
            time.sleep(5)
            return None
        return r.json() if r.status_code == 200 else None
    except:
        return None


def phase2_wiki_api(title):
    no_dots = title.replace(". ", " ")
    for t in [title, no_dots]:
        for suffix in ["", " (Mormon)", " (Latter Day Saints)", " (Latter-day Saint)"]:
            q = t + suffix
            data = wapi(
                {
                    "action": "query",
                    "prop": "pageimages",
                    "pithumbsize": 800,
                    "titles": q,
                    "format": "json",
                }
            )
            if not data:
                continue
            for pid, info in data.get("query", {}).get("pages", {}).items():
                if pid == "-1":
                    continue
                if "original" in info:
                    return info["original"]["source"]
                if "thumbnail" in info:
                    return info["thumbnail"]["source"]
                break  # page exists but no image
    return None


def phase3_wiki_webfetch(title):
    no_dots = title.replace(". ", " ")
    for t in [title, no_dots]:
        for suffix in ["", "_(Mormon)", "_(Latter_Day_Saints)"]:
            url = f"https://en.wikipedia.org/wiki/{t.replace(' ', '_')}{suffix}"
            try:
                r = session.get(url, timeout=10)
                if r.status_code == 200:
                    m = re.search(
                        r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.I
                    )
                    if m and "upload.wikimedia" in m.group(1):
                        return m.group(1)
            except:
                pass
            time.sleep(0.3)
    return None


def phase4_conference(slug, title):
    lastname = title.split()[-1].lower()
    for year in range(2025, 1995, -1):
        for month in ["04", "10"]:
            url = f"https://www.churchofjesuschrist.org/study/general-conference/{year}/{month}/?lang=eng"
            try:
                r = session.get(url, timeout=10)
                if r.status_code != 200:
                    continue
                pattern = rf'href="(/study/general-conference/{year}/{month}/\d{{2}}{re.escape(lastname)})'
                for talk_path in set(re.findall(pattern, r.text.lower())):
                    tr = session.get(
                        f"https://www.churchofjesuschrist.org{talk_path}?lang=eng",
                        timeout=10,
                    )
                    if tr.status_code == 200:
                        m = re.search(
                            r'<meta\s+property="og:image"\s+content="([^"]+)"',
                            tr.text,
                            re.I,
                        )
                        if m:
                            return re.sub(
                                r"/full/![0-9]+,[0-9]+/",
                                "/full/!1280,1600/",
                                m.group(1),
                            )
            except:
                pass
            time.sleep(0.3)
    return None


def phase7_collections(slug, title):
    lastname = slug.split("-")[-1]
    cols = [
        "general-officers",
        "first-presidency-and-quorum-of-the-twelve-apostles-images",
        "presiding-bishopric-and-presidency-of-the-seventy-images",
    ]
    for col in cols:
        try:
            r = session.get(
                f"https://www.churchofjesuschrist.org/media/collection/{col}?lang=eng",
                timeout=15,
            )
            if r.status_code != 200:
                continue
            for idx, chunk in re.findall(
                r"self\.__next_f\.push\(\[([0-9]+),\"(.*?)\"\]\);?", r.text, re.DOTALL
            ):
                try:
                    dec = chunk.encode().decode("unicode_escape")
                except:
                    dec = chunk
                if lastname in dec.lower():
                    imgs = re.findall(
                        r'"(?:src|image|thumbnail)"\s*:\s*"(https://www\.churchofjesuschrist\.org/imgs/[^"]+)"',
                        dec,
                    )
                    if imgs:
                        return re.sub(
                            r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", imgs[0]
                        )
        except:
            pass
    return None


def try_learn_page(slug):
    r = session.get(
        f"https://www.churchofjesuschrist.org/learn/people/{slug}?lang=eng", timeout=10
    )
    if r.status_code == 200:
        m = re.search(r'<meta property="og:image" content="([^"]+)"', r.text)
        if m:
            return m.group(1)
    return None


def try_church_news(slug, title):
    for site_url in [
        f"https://newsroom.churchofjesuschrist.org/search?q={'+'.join(title.split())}",
        f"https://www.thechurchnews.com/?s={slug.replace('-', '+')}",
    ]:
        try:
            r = session.get(site_url, timeout=10)
            if r.status_code == 200:
                articles = re.findall(
                    r'href="(https://(?:www\.)?(?:thechurchnews|newsroom)\.churchofjesuschrist\.org[^"]+)"',
                    r.text,
                )
                for art_url in articles[:3]:
                    ar = session.get(art_url, timeout=10)
                    if ar.status_code == 200:
                        m = re.search(
                            r'<meta\s+property="og:image"\s+content="([^"]+)"',
                            ar.text,
                            re.I,
                        )
                        if m:
                            return (
                                re.sub(r"width=\d+", "width=1200", m.group(1))
                                if "resizer" in m.group(1)
                                else m.group(1)
                            )
                    time.sleep(0.5)
        except:
            pass
    return None


results = {}
for pid, slug, title in people:
    jpg = os.path.join(UPLOADS, f"{slug}.jpg")
    png = os.path.join(UPLOADS, f"{slug}.png")
    if os.path.exists(jpg) and os.path.getsize(jpg) > 2000:
        print(f"  SKIP {slug}")
        results[slug] = {"found": True}
        continue
    if os.path.exists(png) and os.path.getsize(png) > 2000:
        print(f"  SKIP {slug}")
        results[slug] = {"found": True}
        continue

    print(f"\n{title:30s} ({slug})", flush=True)
    found = None
    phase = None

    print(f"  P1 (learn)...", end=" ", flush=True)
    found = try_learn_page(slug)
    print("FOUND" if found else "not found")

    if not found:
        print(f"  P2 (wiki api)...", end=" ", flush=True)
        found = phase2_wiki_api(title)
        print("FOUND" if found else "not found")

    if not found:
        print(f"  P3 (wiki web)...", end=" ", flush=True)
        found = phase3_wiki_webfetch(title)
        print("FOUND" if found else "not found")

    if not found:
        print(f"  P4 (conf)...", end=" ", flush=True)
        found = phase4_conference(slug, title)
        print("FOUND" if found else "not found")

    if not found:
        print(f"  P5 (news)...", end=" ", flush=True)
        found = try_church_news(slug, title)
        print("FOUND" if found else "not found")

    if not found:
        print(f"  P7 (collections)...", end=" ", flush=True)
        found = phase7_collections(slug, title)
        print("FOUND" if found else "not found")

    if found:
        r = session.get(found, timeout=20)
        ext = ".jpg"
        ct = r.headers.get("content-type", "")
        if "png" in ct:
            ext = ".png"
        out = os.path.join(UPLOADS, f"{slug}{ext}")
        if r.status_code == 200 and len(r.content) > 2000:
            with open(out, "wb") as f:
                f.write(r.content)
            print(f"  >>> OK ({len(r.content)} bytes) phase P{phase if phase else '?'}")
            results[slug] = {"found": True, "url": found, "bytes": len(r.content)}
        else:
            print(f"  >>> FAIL: HTTP {r.status_code}, {len(r.content)} bytes")
            results[slug] = {"found": False}
    else:
        print(f"  >>> NOT FOUND")
        results[slug] = {"found": False}

    time.sleep(0.5)

found = sum(1 for v in results.values() if v.get("found"))
print(f"\n\nFound: {found}/{len(people)}")
for slug, info in sorted(results.items()):
    print(
        f"  {'OK' if info.get('found') else 'MISS'} {slug:35s} {info.get('bytes', '')}"
    )
