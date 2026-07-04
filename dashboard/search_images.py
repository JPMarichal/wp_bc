import requests, time, os, json, re

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
RESULTS_FILE = "dashboard/search_results.json"

session = requests.Session()
session.verify = False
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

results = {}


def try_learn_page(slug):
    """Phase 1: churchofjesuschrist.org/learn/people/{slug}"""
    url = f"https://www.churchofjesuschrist.org/learn/people/{slug}?lang=eng"
    try:
        r = session.get(url, timeout=15)
        if "og:image" in r.text:
            m = re.search(r'<meta property="og:image" content="([^"]+)"', r.text)
            if m:
                return m.group(1)
    except:
        pass
    return None


def try_wikipedia_api(slug):
    """Phase 2: Wikipedia API with pageimages prop"""
    # Try different name variants
    name_parts = slug.replace("-", " ").title()
    variants = [
        name_parts,
        name_parts + " (Mormon)",
        name_parts + " (Latter Day Saints)",
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
                "https://en.wikipedia.org/w/api.php", params=params, timeout=15
            )
            if r.status_code == 429:
                time.sleep(5)
                continue
            data = r.json()
            pages = data.get("query", {}).get("pages", {})
            for pid, pdata in pages.items():
                if pid != "-1" and "thumbnail" in pdata:
                    return pdata["thumbnail"]["source"]
        except:
            pass
        time.sleep(2)
    return None


def try_webfetch_wikipedia(slug):
    """Phase 3: Wikipedia via webfetch - skip, done by tool"""
    return None


def try_conference_talks(slug):
    """Phase 4: Scan general conference talks"""
    # Build name variants
    name = slug.replace("-", " ").title()
    lastname = name.split()[-1] if name.split() else ""
    first_initial = name[0] if name else ""

    # Search patterns for conference talk URLs
    for year in range(2024, 2017, -1):
        for month in [4, 10]:
            url = f"https://www.churchofjesuschrist.org/study/general-conference/{year}/{month}/?lang=eng"
            try:
                r = session.get(url, timeout=15)
                # Find talks mentioning this person
                talks = re.findall(
                    rf'href="([^"]*{year}/{month}/\d{{2}}{lastname.lower()}[^"]*)"',
                    r.text.lower(),
                )
                for talk_path in set(talks):
                    talk_url = f"https://www.churchofjesuschrist.org{talk_path}"
                    tr = session.get(talk_url, timeout=15)
                    m = re.search(
                        r'<meta property="og:image" content="([^"]+)"', tr.text
                    )
                    if m:
                        og = m.group(1)
                        # Resize URL
                        resized = re.sub(
                            r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", og
                        )
                        return resized
            except:
                pass
            time.sleep(1)
    return None


def try_church_news(slug):
    """Phase 5: Church News / Newsroom"""
    name = slug.replace("-", " ").title()
    # Search thechurchnews.com
    search_url = f"https://www.thechurchnews.com/?s={slug.replace('-', '+')}"
    try:
        r = session.get(search_url, timeout=15)
        # Look for article links
        articles = re.findall(r'href="(https://www\.thechurchnews\.com/[^"]+)"', r.text)
        for art_url in articles[:5]:
            ar = session.get(art_url, timeout=15)
            m = re.search(r'<meta property="og:image" content="([^"]+)"', ar.text)
            if m:
                og = m.group(1)
                # Resize Church News images
                if "resizer" in og:
                    og = re.sub(r"width=\d+", "width=1200", og)
                return og
            time.sleep(1)
    except:
        pass

    # Try newsroom
    try:
        r = session.get(
            f"https://newsroom.churchofjesuschrist.org/search?q={slug.replace('-', '+')}",
            timeout=15,
        )
        articles = re.findall(
            r'href="(https://newsroom\.churchofjesuschrist\.org/[^"]+)"', r.text
        )
        for art_url in articles[:5]:
            ar = session.get(art_url, timeout=15)
            m = re.search(r'<meta property="og:image" content="([^"]+)"', ar.text)
            if m:
                return m.group(1)
            time.sleep(1)
    except:
        pass
    return None


def try_church_history(slug):
    """Phase 6: Church History Database"""
    name = slug.replace("-", " ").title()
    try:
        r = session.get(f"https://history.churchofjesuschrist.org/chd/", timeout=15)
        search_url = f"https://history.churchofjesuschrist.org/chd/search?query={slug.replace('-', '+')}"
        sr = session.get(search_url, timeout=15)
        m = re.search(r'<img[^>]*src="([^"]*upload[^"]*)"', sr.text)
        if m:
            return m.group(1)
    except:
        pass
    return None


def try_deseret_news(slug):
    """Phase 8: Deseret News"""
    try:
        r = session.get(
            f"https://www.deseret.com/search/{slug.replace('-', ' ')}", timeout=15
        )
        m = re.search(r'<meta property="og:image" content="([^"]+)"', r.text)
        if m:
            return m.group(1)
    except:
        pass
    return None


# Run all phases
for slug in people:
    print(f"\n=== {slug} ===")
    found_url = None

    # Phase 1: Learn pages
    print("  Phase 1 (learn page)...", end=" ")
    url = try_learn_page(slug)
    if url:
        print(f"FOUND: {url[:80]}")
        found_url = url
    else:
        print("not found")

    # Phase 2: Wikipedia API
    if not found_url:
        print("  Phase 2 (Wikipedia API)...", end=" ")
        url = try_wikipedia_api(slug)
        if url:
            print(f"FOUND: {url[:80]}")
            found_url = url
        else:
            print("not found")

    # Phase 4: Conference talks
    if not found_url:
        print("  Phase 4 (conference talks)...", end=" ")
        url = try_conference_talks(slug)
        if url:
            print(f"FOUND: {url[:80]}")
            found_url = url
        else:
            print("not found")

    # Phase 5: Church News
    if not found_url:
        print("  Phase 5 (Church News)...", end=" ")
        url = try_church_news(slug)
        if url:
            print(f"FOUND: {url[:80]}")
            found_url = url
        else:
            print("not found")

    # Phase 6: Church History
    if not found_url:
        print("  Phase 6 (CHD)...", end=" ")
        url = try_church_history(slug)
        if url:
            print(f"FOUND: {url[:80]}")
            found_url = url
        else:
            print("not found")

    # Phase 8: Deseret News
    if not found_url:
        print("  Phase 8 (Deseret News)...", end=" ")
        url = try_deseret_news(slug)
        if url:
            print(f"FOUND: {url[:80]}")
            found_url = url
        else:
            print("not found")

    results[slug] = {"found": found_url is not None, "url": found_url}
    if found_url:
        print(f"  >>> IMAGE FOUND: {found_url}")
    else:
        print(f"  >>> NO IMAGE FOUND")

    time.sleep(1)

# Save results
with open(RESULTS_FILE, "w") as f:
    json.dump(results, f, indent=2)

found_count = sum(1 for v in results.values() if v["found"])
print(f"\n\nResults: {found_count}/{len(people)} images found")
print(f"Saved to {RESULTS_FILE}")
