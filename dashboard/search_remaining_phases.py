import requests, time, os, json, re, sys, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"
RESULTS_FILE = "dashboard/remaining_phases_results.json"

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

# Only the 18 people still needing images (david-a-smith and henry-harriman already have)
people = [
    (969, "alexander-d-acheson", "Alexander D. Acheson"),
    (999, "b-lloyd-poelman", "B. Lloyd Poelman"),
    (1704, "christian-whitmer", "Christian Whitmer"),
    (1063, "clarissa-a-beesley", "Clarissa A. Beesley"),
    (1073, "colleen-b-lemmon", "Colleen B. Lemmon"),
    (1112, "dessie-grant-boyle", "Dessie Grant Boyle"),
    (1119, "donna-d-sorensen", "Donna D. Sorensen"),
    (1120, "dorothy-p-holt", "Dorothy P. Holt"),
    (1121, "dorthea-c-murdock", "Dorthea C. Murdock"),
    (1131, "edith-hunter-lambert", "Edith Hunter Lambert"),
    (1141, "eileen-r-dunyon", "Eileen R. Dunyon"),
    (1145, "elbert-r-curtis", "Elbert R. Curtis"),
    (1168, "florence-r-lane", "Florence R. Lane"),
    (1176, "g-carlos-smith", "G. Carlos Smith"),
    (1189, "george-miller", "George Miller"),
    (1193, "george-r-hill", "George R. Hill"),
    (1221, "helen-s-williams", "Helen S. Williams"),
    (1708, "hiram-page", "Hiram Page"),
]

results = {}


def download(url, path):
    try:
        r = session.get(url, timeout=30)
        if r.status_code == 200 and len(r.content) > 2000:
            with open(path, "wb") as f:
                f.write(r.content)
            return len(r.content)
    except:
        pass
    return None


# ── Phase 4: Conference talks ──
def conference_scan(slug, title, timeout=120):
    lastname = title.split()[-1].lower()
    for year in range(2025, 1995, -1):
        for month in ["04", "10"]:
            if time.time() > timeout:
                return None
            url = f"https://www.churchofjesuschrist.org/study/general-conference/{year}/{month}/?lang=eng"
            try:
                r = session.get(url, timeout=10)
                if r.status_code != 200:
                    continue
                # Modern format: number+lastname
                pattern = rf'href="(/study/general-conference/{year}/{month}/\d{{2}}{re.escape(lastname)})'
                talks = re.findall(pattern, r.text.lower())
                for talk_path in set(talks):
                    talk_url = (
                        f"https://www.churchofjesuschrist.org{talk_path}?lang=eng"
                    )
                    tr = session.get(talk_url, timeout=10)
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
    return None


# ── Phase 5: Church News articles ──
def churchnews_scan(slug, title, timeout=120):
    name = title.lower().replace(". ", " ")
    words = name.split()
    lastname = words[-1] if words else slug.split("-")[-1]
    try:
        # Try newsroom
        r = session.get(
            f"https://newsroom.churchofjesuschrist.org/search?q={'+'.join(words)}",
            timeout=10,
        )
        if r.status_code == 200:
            articles = re.findall(
                r'href="(https://newsroom\.churchofjesuschrist\.org/article/[^"]+)"',
                r.text,
            )
            for art_url in articles[:5]:
                if time.time() > timeout:
                    return None
                ar = session.get(art_url, timeout=10)
                if ar.status_code == 200:
                    m = re.search(
                        r'<meta\s+property="og:image"\s+content="([^"]+)"',
                        ar.text,
                        re.I,
                    )
                    if m and "resizer" in m.group(1):
                        return re.sub(r"width=\d+", "width=1200", m.group(1))
                    if m:
                        return m.group(1)
                time.sleep(0.5)
    except:
        pass
    # Try thechurchnews.com
    try:
        r = session.get(
            f"https://www.thechurchnews.com/?s={'+'.join(words)}", timeout=10
        )
        if r.status_code == 200:
            articles = re.findall(
                r'href="(https://www\.thechurchnews\.com/[^"]+/(?:alive|alerts|archives|author|category|date|entertainment|faith|faith-family|fyi|lifestyle|local|nation-world|news|obituaries|opinion|people|pets|pictures|pressroom|religion|scitech|sports|temples|travel|video)/[^"]+)"',
                r.text,
            )
            for art_url in articles[:10]:
                if time.time() > timeout:
                    return None
                ar = session.get(art_url, timeout=10)
                if ar.status_code == 200:
                    m = re.search(
                        r'<meta\s+property="og:image"\s+content="([^"]+)"',
                        ar.text,
                        re.I,
                    )
                    if m:
                        return m.group(1)
                time.sleep(0.5)
    except:
        pass
    return None


# ── Phase 6: Church History Database ──
def chd_scan(slug, title, timeout=60):
    try:
        r = session.get(
            f"https://history.churchofjesuschrist.org/chd/search?query={'+'.join(title.split())}",
            timeout=10,
        )
        if r.status_code == 200:
            imgs = re.findall(
                r'<img[^>]*src="([^"]*chd[^"]*\.(?:jpg|png))"', r.text, re.I
            )
            if imgs:
                return (
                    "https://history.churchofjesuschrist.org" + imgs[0]
                    if imgs[0].startswith("/")
                    else imgs[0]
                )
    except:
        pass
    return None


# ── Phase 7: Collections ──
def collections_scan(slug, title, timeout=60):
    lastname = slug.split("-")[-1]
    collections = [
        "general-officers",
        "first-presidency-and-quorum-of-the-twelve-apostles-images",
        "presiding-bishopric-and-presidency-of-the-seventy-images",
    ]
    for col in collections:
        if time.time() > timeout:
            return None
        try:
            r = session.get(
                f"https://www.churchofjesuschrist.org/media/collection/{col}?lang=eng",
                timeout=15,
            )
            if r.status_code != 200:
                continue
            chunks = re.findall(
                r"self\.__next_f\.push\(\[([0-9]+),\"(.*?)\"\]\);?", r.text, re.DOTALL
            )
            for idx, chunk in chunks:
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


# ══════════════════════════════════════════════════
for pid, slug, title in people:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path) and os.path.getsize(jpg_path) > 2000:
        print(f"  SKIP {slug}")
        results[slug] = {"found": True, "url": "EXISTS"}
        continue

    print(f"\n{title:30s} ({slug})", flush=True)
    found = None
    phase = None

    deadline = time.time() + 120  # 2 min per person

    # Phase 4: Conference
    if not found:
        print(f"  Phase 4 (conference)...", end=" ", flush=True)
        result = conference_scan(slug, title, deadline)
        if result:
            found = result
            phase = 4
            print("FOUND")
        else:
            print("not found")

    # Phase 5: Church News
    if not found:
        print(f"  Phase 5 (church news)...", end=" ", flush=True)
        result = churchnews_scan(slug, title, deadline)
        if result:
            found = result
            phase = 5
            print("FOUND")
        else:
            print("not found")

    # Phase 6: CHD
    if not found:
        print(f"  Phase 6 (CHD)...", end=" ", flush=True)
        result = chd_scan(slug, title, deadline)
        if result:
            found = result
            phase = 6
            print("FOUND")
        else:
            print("not found")

    # Phase 7: Collections
    if not found:
        print(f"  Phase 7 (collections)...", end=" ", flush=True)
        result = collections_scan(slug, title, deadline)
        if result:
            found = result
            phase = 7
            print("FOUND")
        else:
            print("not found")

    if found:
        sz = download(found, jpg_path)
        if sz:
            print(f"  >>> OK ({sz} bytes) phase {phase}")
            results[slug] = {"found": True, "phase": phase, "bytes": sz}
        else:
            print(f"  >>> download failed")
            results[slug] = {"found": False, "error": "download_failed"}
    else:
        print(f"  >>> NOT FOUND")
        results[slug] = {"found": False, "error": "no_source"}

    time.sleep(0.5)

with open(RESULTS_FILE, "w") as f:
    json.dump(results, f, indent=2)

found = sum(1 for v in results.values() if v.get("found"))
print(f"\n\nFound: {found}/{len(people)}")
for slug, info in sorted(results.items()):
    if info.get("found"):
        print(f"  OK  {slug:35s} phase={info['phase']} {info.get('bytes', 0)} bytes")
    else:
        print(f"  MISS {slug:35s}")
