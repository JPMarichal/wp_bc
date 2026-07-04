import requests, time, os, json, re, sys, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

people = [
    "alexander-d-acheson",
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
    "eileen-r-dunyon",
    "elbert-r-curtis",
    "florence-r-lane",
    "g-carlos-smith",
    "george-miller",
    "george-r-hill",
    "helen-s-williams",
    "henry-harriman",
    "hiram-page",
]

OUTPUT_DIR = "wp-content/uploads/2026/07"
RESULTS_FILE = "dashboard/search_results_v2.json"

# Global session with timeouts
session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

results = {}


def safe_get(url, timeout=15):
    try:
        return session.get(url, timeout=timeout)
    except:
        return None


def try_learn_page(slug):
    url = f"https://www.churchofjesuschrist.org/learn/people/{slug}?lang=eng"
    r = safe_get(url)
    if r and r.status_code == 200:
        m = re.search(r'<meta property="og:image" content="([^"]+)"', r.text)
        if m:
            return m.group(1)
    return None


def try_wikipedia_api(slug):
    name = slug.replace("-", " ").title()
    # Handle dots in initials: "B. Lloyd Poelman" -> "B. Lloyd Poelman"
    variants = [
        name,
        name + " (Mormon)",
        name + " (Latter Day Saints)",
        name.replace(". ", " "),
        name.replace(". ", " ") + " (Mormon)",
    ]
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
                "https://en.wikipedia.org/w/api.php", params=params, timeout=10
            )
            if r.status_code == 429:
                time.sleep(5)
                continue
            if r.status_code != 200:
                continue
            data = r.json()
            pages = data.get("query", {}).get("pages", {})
            for pid, pdata in pages.items():
                if pid != "-1":
                    if "original" in pdata:
                        return pdata["original"]["source"]
                    if "thumbnail" in pdata:
                        return pdata["thumbnail"]["source"]
            # If we got a page but no image, return "PAGE_FOUND" marker
            for pid, pdata in pages.items():
                if pid != "-1":
                    return None  # page exists but no image
        except:
            pass
        time.sleep(1)
    return None


def try_conference_talks(slug):
    name = slug.replace("-", " ").title()
    words = name.split()
    lastname = words[-1].lower() if words else ""
    first_initial = words[0][0].lower() if words and words[0] else ""

    for year in range(2025, 2015, -1):
        for month in ["04", "10"]:
            url = f"https://www.churchofjesuschrist.org/study/general-conference/{year}/{month}/?lang=eng"
            r = safe_get(url)
            if not r or r.status_code != 200:
                continue
            text = r.text

            # Modern format: /study/general-conference/YYYY/MM/NNNNlastname
            pattern = rf'href="(/study/general-conference/{year}/{month}/\d{{2}}{re.escape(lastname)})'
            talks = re.findall(pattern, text.lower())
            for talk_path in set(talks):
                talk_url = f"https://www.churchofjesuschrist.org{talk_path}?lang=eng"
                tr = safe_get(talk_url)
                if tr and tr.status_code == 200:
                    m = re.search(
                        r'<meta property="og:image" content="([^"]+)"', tr.text
                    )
                    if m:
                        og = m.group(1)
                        resized = re.sub(
                            r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", og
                        )
                        return resized
            time.sleep(0.5)
    return None


def try_church_news(slug):
    name = slug.replace("-", " ").title()
    query = slug.replace("-", "+")

    urls_to_try = [
        f"https://www.thechurchnews.com/?s={query}",
        f"https://newsroom.churchofjesuschrist.org/search?q={query}",
    ]
    for search_url in urls_to_try:
        r = safe_get(search_url)
        if not r or r.status_code != 200:
            continue
        # Find article links
        articles = re.findall(
            r'href="(https://(?:www\.)?(?:thechurchnews|newsroom)\.churchofjesuschrist\.org[^"]+)"',
            r.text,
        )
        for art_url in articles[:5]:
            ar = safe_get(art_url)
            if ar and ar.status_code == 200:
                m = re.search(r'<meta property="og:image" content="([^"]+)"', ar.text)
                if m:
                    og = m.group(1)
                    if "resizer" in og:
                        og = re.sub(r"width=\d+", "width=1200", og)
                    return og
            time.sleep(0.5)
    return None


def try_church_history(slug):
    try:
        r = session.get(
            f"https://history.churchofjesuschrist.org/chd/search?query={slug.replace('-', '+')}",
            timeout=10,
        )
        if r.status_code == 200:
            m = re.search(r'<img[^>]*src="([^"]*upload[^"]*)"', r.text)
            if m:
                return m.group(1)
    except:
        pass
    return None


def try_deseret_news(slug):
    r = safe_get(f"https://www.deseret.com/search/{slug.replace('-', ' ')}")
    if r and r.status_code == 200:
        m = re.search(r'<meta property="og:image" content="([^"]+)"', r.text)
        if m:
            return m.group(1)
    return None


def try_byu(slug):
    name = slug.replace("-", " ").title()
    r = safe_get(f"https://speeches.byu.edu/?s={slug.replace('-', '+')}")
    if r and r.status_code == 200:
        m = re.search(r'<meta property="og:image" content="([^"]+)"', r.text)
        if m:
            return m.group(1)
    return None


# Run all phases
for slug in people:
    print(f"\n=== {slug} ===", flush=True)
    found_url = None
    found_phase = None

    # Phase 1: Learn pages
    print("  Phase 1 (learn page)...", end=" ", flush=True)
    url = try_learn_page(slug)
    if url:
        print("FOUND")
        found_url = url
        found_phase = 1
    else:
        print("not found")

    # Phase 2: Wikipedia API
    if not found_url:
        print("  Phase 2 (Wikipedia API)...", end=" ", flush=True)
        url = try_wikipedia_api(slug)
        if url:
            print("FOUND")
            found_url = url
            found_phase = 2
        else:
            print("not found")

    # Phase 3: Wikipedia via webfetch (for pages known to exist but no API image)
    # Will be handled manually afterwards

    # Phase 4: Conference talks
    if not found_url:
        print("  Phase 4 (conference talks)...", end=" ", flush=True)
        url = try_conference_talks(slug)
        if url:
            print("FOUND")
            found_url = url
            found_phase = 4
        else:
            print("not found")

    # Phase 5: Church News / Newsroom
    if not found_url:
        print("  Phase 5 (Church News)...", end=" ", flush=True)
        url = try_church_news(slug)
        if url:
            print("FOUND")
            found_url = url
            found_phase = 5
        else:
            print("not found")

    # Phase 6: Church History Database
    if not found_url:
        print("  Phase 6 (CHD)...", end=" ", flush=True)
        url = try_church_history(slug)
        if url:
            print("FOUND")
            found_url = url
            found_phase = 6
        else:
            print("not found")

    # Phase 8a: Deseret News
    if not found_url:
        print("  Phase 8a (Deseret News)...", end=" ", flush=True)
        url = try_deseret_news(slug)
        if url:
            print("FOUND")
            found_url = url
            found_phase = 8
        else:
            print("not found")

    # Phase 8b: BYU Speeches
    if not found_url:
        print("  Phase 8b (BYU)...", end=" ", flush=True)
        url = try_byu(slug)
        if url:
            print("FOUND")
            found_url = url
            found_phase = 8
        else:
            print("not found")

    results[slug] = {
        "found": found_url is not None,
        "url": found_url,
        "phase": found_phase,
    }
    if found_url:
        print(f"  >>> FOUND via phase {found_phase}: {found_url[:100]}")
    else:
        print(f"  >>> NOT FOUND")

    time.sleep(0.5)

# Save results
with open(RESULTS_FILE, "w") as f:
    json.dump(results, f, indent=2)

found_count = sum(1 for v in results.values() if v["found"])
print(f"\n\nResults: {found_count}/{len(people)} images found")
for slug, info in sorted(results.items()):
    status = "FOUND" if info["found"] else "MISSING"
    phase = f"(phase {info['phase']})" if info["phase"] else ""
    print(f"  {slug:35s} {status} {phase}")
print(f"Saved to {RESULTS_FILE}")
