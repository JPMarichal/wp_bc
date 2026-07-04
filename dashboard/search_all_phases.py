import requests, time, os, json, re, sys, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"
RESULTS_FILE = "dashboard/search_all_results.json"

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

# People: (pid, slug, post_title from DB)
people = [
    (969, "alexander-d-acheson", "Alexander D. Acheson"),
    (999, "b-lloyd-poelman", "B. Lloyd Poelman"),
    (1704, "christian-whitmer", "Christian Whitmer"),
    (1063, "clarissa-a-beesley", "Clarissa A. Beesley"),
    (1073, "colleen-b-lemmon", "Colleen B. Lemmon"),
    (1091, "david-a-smith", "David A. Smith"),
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
    (1227, "henry-harriman", "Henry Harriman"),
    (1708, "hiram-page", "Hiram Page"),
]

results = {}


def wapi(params):
    try:
        r = session.get("https://en.wikipedia.org/w/api.php", params=params, timeout=15)
        if r.status_code == 429:
            time.sleep(5)
            return None
        return r.json() if r.status_code == 200 else None
    except:
        return None


# ──────────────────────────────────────────────────
# PHASE 2: Wikipedia API
# ──────────────────────────────────────────────────
def phase2_wikipedia(title):
    for suffix in ["", " (Mormon)", " (Latter Day Saints)", " (Latter-day Saint)"]:
        q = title + suffix
        params = {
            "action": "query",
            "prop": "pageimages",
            "pithumbsize": 800,
            "titles": q,
            "format": "json",
        }
        data = wapi(params)
        if not data:
            continue
        for pid, info in data.get("query", {}).get("pages", {}).items():
            if pid == "-1":
                continue
            if "original" in info:
                return info["original"]["source"]
            if "thumbnail" in info:
                return info["thumbnail"]["source"]
            return "PAGE_EXISTS_NO_IMAGE"
    return None


# ──────────────────────────────────────────────────
# PHASE 3: Wikipedia via webfetch (requests as browser)
# ──────────────────────────────────────────────────
def phase3_webfetch_wikipedia(title):
    for suffix in ["", "_(Mormon)", "_(Latter_Day_Saints)", "_(Latter-day_Saint)"]:
        url_slug = title.replace(". ", "_").replace(" ", "_") + suffix
        url = f"https://en.wikipedia.org/wiki/{url_slug}"
        try:
            r = session.get(url, timeout=15)
            if r.status_code == 200:
                m = re.search(
                    r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.I
                )
                if m:
                    return m.group(1)
        except:
            pass
    return None


# ──────────────────────────────────────────────────
# PHASE 4: Conference talks
# ──────────────────────────────────────────────────
def phase4_conference(slug, title):
    words = title.split()
    lastname = words[-1].lower() if words else ""
    for year in range(2025, 2015, -1):
        for month in ["04", "10"]:
            url = f"https://www.churchofjesuschrist.org/study/general-conference/{year}/{month}/?lang=eng"
            try:
                r = session.get(url, timeout=15)
                if r.status_code != 200:
                    continue
                pattern = rf'href="(/study/general-conference/{year}/{month}/\d{{2}}{re.escape(lastname)})'
                talks = re.findall(pattern, r.text.lower())
                for talk_path in set(talks):
                    talk_url = (
                        f"https://www.churchofjesuschrist.org{talk_path}?lang=eng"
                    )
                    tr = session.get(talk_url, timeout=15)
                    if tr.status_code == 200:
                        m = re.search(
                            r'<meta\s+property="og:image"\s+content="([^"]+)"',
                            tr.text,
                            re.I,
                        )
                        if m:
                            og = m.group(1)
                            return re.sub(
                                r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", og
                            )
            except:
                pass
            time.sleep(0.3)
    return None


# ──────────────────────────────────────────────────
# PHASE 5: Church News / Newsroom
# ──────────────────────────────────────────────────
def phase5_church_news(slug, title):
    for site_url in [
        f"https://www.thechurchnews.com/?s={slug.replace('-', '+')}",
        f"https://newsroom.churchofjesuschrist.org/search?q={slug.replace('-', '+')}",
    ]:
        try:
            r = session.get(site_url, timeout=15)
            if r.status_code == 200:
                articles = re.findall(
                    r'href="(https://(?:www\.)?(?:thechurchnews|newsroom)\.churchofjesuschrist\.org[^"]+)"',
                    r.text,
                )
                for art_url in articles[:5]:
                    ar = session.get(art_url, timeout=15)
                    if ar.status_code == 200:
                        m = re.search(
                            r'<meta\s+property="og:image"\s+content="([^"]+)"',
                            ar.text,
                            re.I,
                        )
                        if m:
                            og = m.group(1)
                            if "resizer" in og:
                                og = re.sub(r"width=\d+", "width=1200", og)
                            return og
                    time.sleep(0.5)
        except:
            pass
    return None


# ──────────────────────────────────────────────────
# PHASE 6: Church History Database
# ──────────────────────────────────────────────────
def phase6_church_history(slug, title):
    url = f"https://history.churchofjesuschrist.org/chd/search?query={slug.replace('-', '+')}"
    try:
        r = session.get(url, timeout=15)
        if r.status_code == 200:
            # Look for any img tag with upload in src
            imgs = re.findall(r'<img[^>]*src="([^"]*upload[^"]*)"', r.text)
            if imgs:
                return imgs[0]
            # Look for og:image
            m = re.search(
                r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.I
            )
            if m:
                return m.group(1)
    except:
        pass
    return None


# ──────────────────────────────────────────────────
# PHASE 8a: Deseret News
# ──────────────────────────────────────────────────
def phase8_deseret(slug, title):
    try:
        r = session.get(
            f"https://www.deseret.com/search/{slug.replace('-', ' ')}", timeout=15
        )
        if r.status_code == 200:
            m = re.search(
                r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.I
            )
            if m:
                return m.group(1)
    except:
        pass
    return None


# ──────────────────────────────────────────────────
# PHASE 8b: BYU Speeches
# ──────────────────────────────────────────────────
def phase8_byu(slug, title):
    try:
        r = session.get(
            f"https://speeches.byu.edu/?s={slug.replace('-', '+')}", timeout=15
        )
        if r.status_code == 200:
            m = re.search(
                r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.I
            )
            if m:
                return m.group(1)
    except:
        pass
    return None


# ══════════════════════════════════════════════════
# MAIN LOOP
# ══════════════════════════════════════════════════
for pid, slug, title in people:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    # Skip if already downloaded properly
    if os.path.exists(jpg_path) and os.path.getsize(jpg_path) > 1000:
        print(f"  SKIP {slug} (already have valid image)")
        results[slug] = {
            "found": True,
            "url": "EXISTS",
            "phase": 0,
            "bytes": os.path.getsize(jpg_path),
        }
        continue

    print(f"\n{'=' * 60}")
    print(f"  {title} ({slug})")
    print(f"{'=' * 60}", flush=True)
    found_url = None
    found_phase = None

    # ── Phase 2: Wikipedia API ──
    print(f"  Phase 2 (Wikipedia API)...", end=" ", flush=True)
    result = phase2_wikipedia(title)
    if result and result != "PAGE_EXISTS_NO_IMAGE":
        found_url = result
        found_phase = 2
        print("FOUND")
    elif result == "PAGE_EXISTS_NO_IMAGE":
        print("page exists, no image")
    else:
        print("not found")

    # ── Phase 3: Wikipedia webfetch ──
    if not found_url:
        print(f"  Phase 3 (Wikipedia webfetch)...", end=" ", flush=True)
        result = phase3_webfetch_wikipedia(title)
        if result:
            found_url = result
            found_phase = 3
            print("FOUND")
        else:
            print("not found")

    # ── Phase 4: Conference talks ──
    if not found_url:
        print(f"  Phase 4 (conference talks)...", end=" ", flush=True)
        result = phase4_conference(slug, title)
        if result:
            found_url = result
            found_phase = 4
            print("FOUND")
        else:
            print("not found")

    # ── Phase 5: Church News ──
    if not found_url:
        print(f"  Phase 5 (Church News)...", end=" ", flush=True)
        result = phase5_church_news(slug, title)
        if result:
            found_url = result
            found_phase = 5
            print("FOUND")
        else:
            print("not found")

    # ── Phase 6: Church History ──
    if not found_url:
        print(f"  Phase 6 (CHD)...", end=" ", flush=True)
        result = phase6_church_history(slug, title)
        if result:
            found_url = result
            found_phase = 6
            print("FOUND")
        else:
            print("not found")

    # ── Phase 8a: Deseret News ──
    if not found_url:
        print(f"  Phase 8a (Deseret News)...", end=" ", flush=True)
        result = phase8_deseret(slug, title)
        if result:
            found_url = result
            found_phase = 8
            print("FOUND")
        else:
            print("not found")

    # ── Phase 8b: BYU ──
    if not found_url:
        print(f"  Phase 8b (BYU)...", end=" ", flush=True)
        result = phase8_byu(slug, title)
        if result:
            found_url = result
            found_phase = 8
            print("FOUND")
        else:
            print("not found")

    # ── Download if found ──
    if found_url:
        try:
            r = session.get(found_url, timeout=30)
            if r.status_code == 200 and len(r.content) > 1000:
                with open(jpg_path, "wb") as f:
                    f.write(r.content)
                print(
                    f"  >>> DOWNLOADED: {slug}.jpg ({len(r.content)} bytes) from phase {found_phase}"
                )
                results[slug] = {
                    "found": True,
                    "url": found_url,
                    "phase": found_phase,
                    "bytes": len(r.content),
                }
            else:
                print(
                    f"  >>> FAILED DOWNLOAD: HTTP {r.status_code}, {len(r.content)} bytes"
                )
                results[slug] = {
                    "found": False,
                    "url": found_url,
                    "phase": found_phase,
                    "error": "download_failed",
                }
        except Exception as e:
            print(f"  >>> DOWNLOAD ERROR: {e}")
            results[slug] = {
                "found": False,
                "url": found_url,
                "phase": found_phase,
                "error": str(e),
            }
    else:
        print(f"  >>> NO IMAGE FOUND in any phase")
        results[slug] = {
            "found": False,
            "url": None,
            "phase": None,
            "error": "no_source",
        }

    time.sleep(0.5)

# ──────────────────────────────────────────────────
# SUMMARY
# ──────────────────────────────────────────────────
with open(RESULTS_FILE, "w") as f:
    json.dump(results, f, indent=2)

print(f"\n\n{'=' * 60}")
print(f"  RESULTS")
print(f"{'=' * 60}")
found = sum(1 for v in results.values() if v.get("found"))
for slug, info in sorted(results.items()):
    if info.get("found"):
        print(f"  ✅ {slug:35s} phase={info['phase']}  {info.get('bytes', 0)} bytes")
    else:
        print(f"  ❌ {slug:35s} reason={info.get('error', 'unknown')}")
print(f"\n  Found: {found}/{len(people)}")
print(f"  Saved to {RESULTS_FILE}")
