import requests, time, os, json, re, sys, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"
RESULTS_FILE = "dashboard/search_all_results_v3.json"

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

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


# ──────────────────────────────────────────────────
# PHASE 2+3: Wikipedia combined
# ──────────────────────────────────────────────────
def wikipedia_image(title):
    """Try Wikipedia API + webfetch to find an image for a person.
    Phase 2: API with pageimages prop — tries multiple title variants.
    Phase 3: Fetch actual page and extract og:image.
    """
    # Build title variants — handle periods in initials
    # "David A. Smith" → variants with/without period, (Mormon), (Latter Day Saints)
    no_dots = title.replace(". ", " ")
    variants = []
    for t in [title, no_dots]:
        variants.append(t)
        variants.append(t + " (Mormon)")
        variants.append(t + " (Latter Day Saints)")
        variants.append(t + " (Latter-day Saint)")
        # Also try underscore-based
        variants.append(t.replace(" ", "_"))
        variants.append(t.replace(" ", "_") + "_(Mormon)")
    variants = list(dict.fromkeys(variants))  # deduplicate

    # Phase 2: API search + pageimages
    for variant in variants:
        params = {
            "action": "query",
            "prop": "pageimages",
            "pithumbsize": 800,
            "titles": variant,
            "format": "json",
        }
        try:
            r = session.get(
                "https://en.wikipedia.org/w/api.php", params=params, timeout=15
            )
            if r.status_code == 429:
                time.sleep(5)
                continue
            if r.status_code != 200:
                continue
            data = r.json()
            for pid, info in data.get("query", {}).get("pages", {}).items():
                if pid == "-1":
                    continue
                if "original" in info:
                    return ("API", info["original"]["source"])
                if "thumbnail" in info:
                    return ("API", info["thumbnail"]["source"])
                # Page exists but no image — continue to next variant
                break
        except:
            pass
        time.sleep(0.3)

    # Phase 3: Fetch pages directly and extract og:image
    for variant in variants:
        # Convert to URL format
        url_slug = variant.replace(" ", "_")
        url = f"https://en.wikipedia.org/wiki/{url_slug}"
        try:
            r = session.get(url, timeout=15)
            if r.status_code == 200:
                m = re.search(
                    r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.I
                )
                if m and "upload.wikimedia" in m.group(1):
                    return ("WEBFETCH", m.group(1))
        except:
            pass
        time.sleep(0.3)

    return None


# ──────────────────────────────────────────────────
# PHASE 4: Conference talks
# ──────────────────────────────────────────────────
def conference_image(slug, title):
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
def churchnews_image(slug, title):
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
def chd_image(slug, title):
    url = f"https://history.churchofjesuschrist.org/chd/search?query={slug.replace('-', '+')}"
    try:
        r = session.get(url, timeout=15)
        if r.status_code == 200:
            imgs = re.findall(r'<img[^>]*src="([^"]*upload[^"]*)"', r.text)
            if imgs:
                return imgs[0]
            m = re.search(
                r'<meta\s+property="og:image"\s+content="([^"]+)"', r.text, re.I
            )
            if m:
                return m.group(1)
    except:
        pass
    return None


# ──────────────────────────────────────────────────
# PHASE 7: Colección de líderes (churchofjesuschrist.org/media/collection)
# ──────────────────────────────────────────────────
def leaders_collection_image(slug, title):
    """Scan church leaders image collections for matching names"""
    collections = [
        "general-officers",
        "first-presidency-and-quorum-of-the-twelve-apostles-images",
        "presiding-bishopric-and-presidency-of-the-seventy-images",
        "young-women-general-presidents-images",
        "relief-society-general-presidents-images",
        "primary-general-presidents-images",
        "presidents-of-the-church-of-jesus-christ-of-latter-day-saints-images",
        "joseph-smith-images",
    ]

    # Build name parts from slug for matching
    slug_parts = slug.split("-")
    lastname = slug_parts[-1] if slug_parts else ""
    # Also get full name without periods
    search_name = title.lower().replace(".", "")

    for col in collections:
        url = f"https://www.churchofjesuschrist.org/media/collection/{col}?lang=eng"
        try:
            r = session.get(url, timeout=15)
            if r.status_code != 200:
                continue

            # Parse RSC chunks
            chunks = re.findall(
                r"self\.__next_f\.push\(\[([0-9]+),\"(.*?)\"\]\);?", r.text, re.DOTALL
            )
            for idx, chunk in chunks:
                try:
                    dec = chunk.encode().decode("unicode_escape")
                except:
                    dec = chunk

                # Look for name + image pairs in decoded RSC payload
                # Find names first
                names = re.findall(r'"(?:title|name|altText)"\s*:\s*"([^"]+)"', dec)
                imgs = re.findall(
                    r'"(?:src|image|thumbnail)"\s*:\s*"(https://www\.churchofjesuschrist\.org/imgs/[^"]+)"',
                    dec,
                )

                for n in names:
                    n_clean = n.replace("\\u0026", "&").replace("\\/", "/").lower()
                    # Check if last name matches
                    if lastname in n_clean:
                        # Check if significant parts match
                        significant = [p for p in slug_parts if len(p) > 1]
                        matches = sum(1 for p in significant if p in n_clean)
                        if matches >= 2 or (matches >= 1 and len(significant) <= 1):
                            # Found! Get image
                            for img in imgs:
                                full = re.sub(
                                    r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", img
                                )
                                full = re.sub(
                                    r"/full/[^/]+/", "/full/!1280,1600/", full
                                )
                                return full
        except:
            pass

    return None


# ══════════════════════════════════════════════════
# MAIN LOOP
# ══════════════════════════════════════════════════
for pid, slug, title in people:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path) and os.path.getsize(jpg_path) > 1000:
        sz = os.path.getsize(jpg_path)
        if sz != 78384:  # Skip BYU generic size
            print(f"  SKIP {slug} (already have valid image, {sz} bytes)")
            results[slug] = {"found": True, "url": "EXISTS", "phase": 0, "bytes": sz}
            continue

    # Remove false-positive BYU generic if present
    if os.path.exists(jpg_path) and os.path.getsize(jpg_path) == 78384:
        os.remove(jpg_path)
        print(f"  REMOVED false positive for {slug}")

    print(f"\n{'=' * 60}")
    print(f"  {title} ({slug})")
    print(f"{'=' * 60}", flush=True)
    found = None
    found_phase = None

    # ── Phases 2+3: Wikipedia ──
    print(f"  Phases 2+3 (Wikipedia)...", end=" ", flush=True)
    result = wikipedia_image(title)
    if result:
        source, url = result
        # Verify this is a real image, not a placeholder
        try:
            vr = session.get(url, timeout=15)
            if vr.status_code == 200 and len(vr.content) > 2000:
                found = url
                found_phase = 2 if source == "API" else 3
                print(f"FOUND ({source}, {len(vr.content)} bytes)")
            else:
                print(f"found but download failed ({len(vr.content)} bytes)")
        except:
            print("found but download error")
    else:
        print("not found")

    # ── Phase 4: Conference talks ──
    if not found:
        print(f"  Phase 4 (conference talks)...", end=" ", flush=True)
        result = conference_image(slug, title)
        if result:
            found = result
            found_phase = 4
            print("FOUND")
        else:
            print("not found")

    # ── Phase 5: Church News ──
    if not found:
        print(f"  Phase 5 (Church News)...", end=" ", flush=True)
        result = churchnews_image(slug, title)
        if result:
            found = result
            found_phase = 5
            print("FOUND")
        else:
            print("not found")

    # ── Phase 6: Church History ──
    if not found:
        print(f"  Phase 6 (CHD)...", end=" ", flush=True)
        result = chd_image(slug, title)
        if result:
            found = result
            found_phase = 6
            print("FOUND")
        else:
            print("not found")

    # ── Phase 7: Colección de líderes ──
    if not found:
        print(f"  Phase 7 (colección)...", end=" ", flush=True)
        result = leaders_collection_image(slug, title)
        if result:
            found = result
            found_phase = 7
            print("FOUND")
        else:
            print("not found")

    # ── Download if found ──
    if found:
        try:
            r = session.get(found, timeout=30)
            if r.status_code == 200 and len(r.content) > 2000:
                with open(jpg_path, "wb") as f:
                    f.write(r.content)
                print(
                    f"  >>> OK: {slug}.jpg ({len(r.content)} bytes) from phase {found_phase}"
                )
                results[slug] = {
                    "found": True,
                    "url": found,
                    "phase": found_phase,
                    "bytes": len(r.content),
                }
            else:
                print(f"  >>> FAIL: HTTP {r.status_code}, {len(r.content)} bytes")
                results[slug] = {"found": False, "error": "download_failed"}
        except Exception as e:
            print(f"  >>> ERROR: {e}")
            results[slug] = {"found": False, "error": str(e)}
    else:
        print(f"  >>> NOT FOUND in any phase")
        results[slug] = {"found": False, "error": "no_source"}

    time.sleep(0.5)

# ── SUMMARY ──
with open(RESULTS_FILE, "w") as f:
    json.dump(results, f, indent=2)

print(f"\n\n{'=' * 60}")
print(f"  RESULTS")
print(f"{'=' * 60}")
found = sum(1 for v in results.values() if v.get("found"))
for slug, info in sorted(results.items()):
    if info.get("found"):
        print(f"  OK  {slug:35s} phase={info['phase']}  {info.get('bytes', 0)} bytes")
    else:
        print(f"  MISS {slug:35s} reason={info.get('error', 'unknown')}")
print(f"\n  Found: {found}/{len(people)}")
print(f"  Saved to {RESULTS_FILE}")
