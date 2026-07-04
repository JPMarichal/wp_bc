import requests, os, re, time, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)

# Scan all conferences from 2000 to 2026
years_months = []
for y in range(2000, 2027):
    years_months.append(f"{y}/04")
    years_months.append(f"{y}/10")

# Remove future ones
import datetime

now = datetime.datetime.now()
years_months = [ym for ym in years_months if int(ym[:4]) <= now.year]

# People we're looking for - build list of last names
remaining_slugs = [
    "b-lloyd-poelman",
    "burton-k-farnsworth",
    "christian-whitmer",
    "clarissa-a-beesley",
    "colleen-b-lemmon",
    "daniel-s-miles",
    "david-a-smith",
    "dessie-grant-boyle",
    "donna-d-sorensen",
    "dorothy-p-holt",
    "dorthea-c-murdock",
    "edith-hunter-lambert",
    "edward-hunter",
    "eileen-r-dunyon",
    "elbert-r-curtis",
    "florence-h-richards",
    "florence-r-lane",
    "g-carlos-smith",
    "george-goddard",
    "george-miller",
    "george-r-hill",
    "george-reynolds",
    "helen-s-williams",
    "henry-harriman",
    "hiram-page",
    "hortense-h-c-smith",
    "isabelle-salmon-ross",
    "j-ballard-washburn",
    "j-devn-cornish",
    "j-hugh-baird",
    "j-kent-jolley",
    "j-richard-clarke",
    "james-foster",
    "james-h-hart",
    "james-m-paramore",
    "janet-murdock-thompson",
    "janette-hales-beckham",
    "jayne-b-malan",
    "jesse-gause",
    "joanne-b-doxey",
    "john-corrill",
]
# Build the last names to look for
target_lastnames = set()
for slug in remaining_slugs:
    parts = slug.split("-")
    target_lastnames.add(parts[-1])

# Also add alternative: for "g-carlos-smith" last name is "smith"
# For "j-kent-jolley" last name is "jolley"
# For "hortense-h-c-smith" last name is "smith" (but already have smith)

print(
    f"Scanning {len(years_months)} conferences for {len(target_lastnames)} target speakers..."
)
print(f"Targets: {sorted(target_lastnames)}")

found_talks = {}  # lastname -> [(talk_url, og_image)]

for ym in years_months:
    url = f"https://www.churchofjesuschrist.org/study/general-conference/{ym}?lang=eng"
    try:
        r = session.get(url, timeout=15)
        if r.status_code != 200:
            continue

        links = re.findall(
            r"href=\"(/study/general-conference/[^\"]+/[0-9]+[a-z]+)\?lang=eng\"",
            r.text,
        )

        for link in links:
            m = re.match(r"/study/general-conference/[^/]+/[^/]+/[0-9]+([a-z]+)", link)
            if m:
                last = m.group(1)
                if last in target_lastnames and last not in found_talks:
                    full_url = (
                        "https://www.churchofjesuschrist.org" + link + "?lang=eng"
                    )
                    try:
                        tr = session.get(full_url, timeout=15)
                        og = re.search(
                            r'<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"]',
                            tr.text,
                        )
                        if og:
                            found_talks[last] = (full_url, og.group(1))
                            print(f"FOUND {last}: {full_url}")
                    except:
                        pass
    except:
        pass

    completed = years_months.index(ym) + 1
    if completed % 10 == 0:
        print(
            f"  Progress: {completed}/{len(years_months)} conferences, found {len(found_talks)}/{len(target_lastnames)}"
        )

print(f"\nFound {len(found_talks)}/{len(target_lastnames)} speakers:")
for last, (talk_url, img_url) in sorted(found_talks.items()):
    print(f"  {last}: {img_url}")
