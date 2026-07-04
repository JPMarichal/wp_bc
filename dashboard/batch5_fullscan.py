import requests, os, re, time, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"

remaining = [
    ("b-lloyd-poelman", "B. Lloyd Poelman"),
    ("burton-k-farnsworth", "Burton K. Farnsworth"),
    ("christian-whitmer", "Christian Whitmer"),
    ("clarissa-a-beesley", "Clarissa A. Beesley"),
    ("colleen-b-lemmon", "Colleen B. Lemmon"),
    ("daniel-s-miles", "Daniel S. Miles"),
    ("david-a-smith", "David A. Smith"),
    ("dessie-grant-boyle", "Dessie Grant Boyle"),
    ("donna-d-sorensen", "Donna D. Sorensen"),
    ("dorothy-p-holt", "Dorothy P. Holt"),
    ("dorthea-c-murdock", "Dorthea C. Murdock"),
    ("edith-hunter-lambert", "Edith Hunter Lambert"),
    ("edward-hunter", "Edward Hunter"),
    ("eileen-r-dunyon", "Eileen R. Dunyon"),
    ("elbert-r-curtis", "Elbert R. Curtis"),
    ("florence-h-richards", "Florence H. Richards"),
    ("florence-r-lane", "Florence R. Lane"),
    ("g-carlos-smith", "G. Carlos Smith"),
    ("george-goddard", "George Goddard"),
    ("george-miller", "George Miller"),
    ("george-r-hill", "George R. Hill"),
    ("george-reynolds", "George Reynolds"),
    ("helen-s-williams", "Helen S. Williams"),
    ("henry-harriman", "Henry Harriman"),
    ("hiram-page", "Hiram Page"),
    ("hortense-h-c-smith", "Hortense H. C. Smith"),
    ("isabelle-salmon-ross", "Isabelle Salmon Ross"),
    ("j-ballard-washburn", "J. Ballard Washburn"),
    ("j-devn-cornish", "J. Devn Cornish"),
    ("j-hugh-baird", "J. Hugh Baird"),
    ("j-kent-jolley", "J. Kent Jolley"),
    ("j-richard-clarke", "J. Richard Clarke"),
    ("james-foster", "James Foster"),
    ("james-h-hart", "James H. Hart"),
    ("james-m-paramore", "James M. Paramore"),
    ("janet-murdock-thompson", "Janet Murdock Thompson"),
    ("janette-hales-beckham", "Janette Hales Beckham"),
    ("jayne-b-malan", "Jayne B. Malan"),
    ("jesse-gause", "Jesse Gause"),
    ("joanne-b-doxey", "Joanne B. Doxey"),
    ("john-corrill", "John Corrill"),
]

session = requests.Session()
session.verify = False
session.headers.update(
    {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
)


def extract_speaker_from_title(title_text):
    """Try to extract last word(s) that look like a name at end of talk title"""
    title_text = title_text.strip()
    words = title_text.split()
    if len(words) < 2:
        return None
    # The last few words might be the speaker name
    # Common patterns: "Talk Title Speaker Name" or "Talk Title By Speaker"
    # Remove session headers
    if title_text.lower().startswith(
        ("saturday ", "sunday ", "sustaining", "church auditing", "priesthood")
    ):
        return None
    # Look for "By " pattern
    if " By " in title_text:
        return title_text.split(" By ")[-1].strip()
    # Otherwise, take last 2-4 words as potential name
    for n_words in [4, 3, 2]:
        potential = " ".join(words[-n_words:])
        # Check if it looks like a name (has uppercase letters, no stop words)
        if any(c.isupper() for c in potential) and len(potential) > 5:
            if not any(
                word.lower() in ("session", "the", "of", "and", "for", "in", "to")
                for word in potential.split()
            ):
                return potential
    return None


def name_matches(speaker_name, slug):
    """Check if speaker name matches the slug"""
    if not speaker_name:
        return False
    speaker_lower = speaker_name.lower()
    slug_lower = slug.lower().replace("-", " ")
    # Direct match
    if slug_lower in speaker_lower or speaker_lower in slug_lower:
        return True
    # Last name match
    slug_parts = slug.split("-")
    # Remove single-letter initials
    significant = [p for p in slug_parts if len(p) > 1]
    last_name = slug_parts[-1]
    # Check if last name appears in speaker name
    if last_name in speaker_lower:
        return True
    # Check all significant parts
    for part in significant:
        if part in speaker_lower:
            return True
    return False


# Priority: most recent conferences first
def get_conference_list():
    confs = []
    for y in range(2026, 2009, -1):
        confs.append(f"{y}/04")
        confs.append(f"{y}/10")
    # For old format (1990s-2000s), the talks might have different structure
    return confs


print("Building comprehensive speaker map...")
conferences = get_conference_list()

# For modern format (2023+): /study/general-conference/YYYY/MM/NNNNlastname
# For old format (pre-2023): /study/general-conference/YYYY/MM/talk-title
speaker_talks = {}  # lastname -> (talk_url, og_image)

for i, conf in enumerate(conferences):
    url = (
        f"https://www.churchofjesuschrist.org/study/general-conference/{conf}?lang=eng"
    )
    try:
        r = session.get(url, timeout=15)
        if r.status_code != 200:
            continue

        text = r.text

        # Try modern format first (2024+)
        modern_links = re.findall(
            r"href=\"(/study/general-conference/[^\"]+/[0-9]+[a-z]+)\?lang=eng\"", text
        )

        if modern_links:
            # Modern format: URL directly contains last name
            for link in modern_links:
                m = re.match(
                    r"/study/general-conference/[^/]+/[^/]+/[0-9]+([a-z]+)", link
                )
                if m:
                    last = m.group(1)
                    if last not in speaker_talks:
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
                                speaker_talks[last] = (full_url, og.group(1))
                        except:
                            pass
        else:
            # Old format: extract speaker names from link text
            talk_pattern = (
                r"<a[^>]*href=\"(/study/general-conference/"
                + conf.replace("/", "/")
                + r"/[^\"?]+)[^>]*>(.*?)</a>"
            )
            talks = re.findall(talk_pattern, text, re.DOTALL)

            for href, inner in talks:
                # Extract speaker name
                inner_clean = re.sub(r"<[^>]+>", " ", inner)
                inner_clean = re.sub(r"\s+", " ", inner_clean).strip()

                # Check each remaining person
                for slug, title in remaining:
                    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
                    if os.path.exists(jpg_path):
                        continue

                    if name_matches(inner_clean, slug):
                        # Check if we already found this person
                        last = slug.split("-")[-1]
                        if last in speaker_talks:
                            continue

                        full_url = (
                            "https://www.churchofjesuschrist.org" + href + "?lang=eng"
                        )
                        try:
                            tr = session.get(full_url, timeout=15)
                            og = re.search(
                                r'<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"]([^\'"]+)[\'"]',
                                tr.text,
                            )
                            if og:
                                speaker_talks[last] = (full_url, og.group(1))
                                print(
                                    f"  FOUND {slug} in {conf}: {inner_clean[:50]}..."
                                )
                        except:
                            pass
    except Exception as e:
        pass

    if (i + 1) % 10 == 0 or i == len(conferences) - 1:
        found_count = sum(
            1
            for slug, _ in remaining
            if slug.split("-")[-1] in speaker_talks
            or os.path.exists(os.path.join(UPLOADS, f"{slug}.jpg"))
        )
        print(
            f"  Progress: {i + 1}/{len(conferences)} conferences, found so far: {found_count}/{len(remaining)}"
        )

print(f"\nFound {len(speaker_talks)} new speakers via conference pages")

# Download the images
downloaded = 0
not_found = 0

for slug, title in remaining:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if os.path.exists(jpg_path):
        downloaded += 1
        continue

    last = slug.split("-")[-1]
    if last in speaker_talks:
        talk_url, og_url = speaker_talks[last]
        # Download at full size
        try:
            size_url = re.sub(r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", og_url)
            r = session.get(size_url, timeout=30)
            if r.status_code != 200 or len(r.content) < 1000:
                r = session.get(og_url, timeout=30)

            if r.status_code == 200 and len(r.content) > 1000:
                with open(jpg_path, "wb") as f:
                    f.write(r.content)
                print(f"OK     {slug} ({len(r.content)} bytes)")
                downloaded += 1
            else:
                print(f"FAILDL {slug}")
                not_found += 1
        except Exception as e:
            print(f"ERRDL  {slug}: {e}")
            not_found += 1
    else:
        not_found += 1

print(f"\nDone! Downloaded: {downloaded}, Still missing: {not_found}")
print("\nStill missing:")
for slug, title in remaining:
    jpg_path = os.path.join(UPLOADS, f"{slug}.jpg")
    if not os.path.exists(jpg_path):
        print(f"  {slug} - {title}")
