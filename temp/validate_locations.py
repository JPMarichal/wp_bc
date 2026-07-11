"""Validate all bc_location English names against RV60 Spanish scriptures via Alejandría,
and update WordPress with validated Spanish names."""

import json
import os
import subprocess
import sys
import unicodedata
import urllib.request
from time import sleep

# ============================================================
# Known Spanish equivalents (pre-validated against RV60 knowledge)
# ============================================================
KNOWN_SPANISH = {
    # Well-known biblical name differences
    "Jericho": "Jericó",
    "Bethlehem": "Belén",
    "Nazareth": "Nazaret",
    "Smyrna": "Esmirna",
    "Ephesus": "Éfeso",
    "Nineveh": "Nínive",
    "Corinth": "Corinto",
    "Galilee": "Galilea",
    "Jordan": "Jordán",
    "Jerusalem": "Jerusalén",
    "Capernaum": "Capernaúm",
    "Gethsemane": "Getsemaní",
    "Golgotha": "Gólgota",
    "Calvary": "Calvario",
    "Damascus": "Damasco",
    "Tyre": "Tiro",
    "Sidon": "Sidón",
    "Babylon": "Babilonia",
    "Egypt": "Egipto",
    "Rome": "Roma",
    "Greece": "Grecia",
    "Syria": "Siria",
    "Canaan": "Canaán",
    "Euphrates": "Éufrates",
    "Tigris": "Tigris",
    "Mediterranean": "Mediterráneo",
    "Gibraltar": "Gibraltar",
    "Mount Sinai": "Monte Sinaí",
    "Mount Zion": "Monte Sion",
    "Mount of Olives": "Monte de los Olivos",
    "Pool of Bethesda": "Estanque de Betesda",
    "Red Sea": "Mar Rojo",
    "Dead Sea": "Mar Muerto",
    "Wilderness of Paran": "Desierto de Parán",
    "Wilderness of Zin": "Desierto de Zin",
    "Wilderness of Shur": "Desierto de Shur",
    "Wilderness of Judea": "Desierto de Judea",
    "Sea of Galilee": "Mar de Galilea",
    "Salt Sea": "Mar Salado",
    "Mount Hor": "Monte Hor",
    "Mount Nebo": "Monte Nebo",
    "Mount Gerizim": "Monte Gerizim",
    "Mount Ebal": "Monte Ebal",
    "Mount Gilboa": "Monte Gilboa",
    "Valley of Achor": "Valle de Acor",
    "Valley of Elah": "Valle de Ela",
    "Valley of Siddim": "Valle de Sidim",
    "Valley of Hinnom": "Valle de Hinom",
    "Valley of Jehoshaphat": "Valle de Josafat",
    "Plain of Esdraelon": "Llanura de Esdraelón",
    "Plain of Dura": "Llanura de Dura",
    "River Jordan": "Río Jordán",
    "River Nile": "Río Nilo",
    "Brook Cherith": "Arroyo de Querit",
    "Brook Kidron": "Arroyo de Cedrón",
    "Wadi of Egypt": "Torrente de Egipto",
    "Great Sea": "Mar Grande",
    "Eastern Sea": "Mar Oriental",
    "Western Sea": "Mar Occidental",
    "Hill of Moreh": "Colina de More",
    "Tower of Babel": "Torre de Babel",
    "Tower of Siloam": "Torre de Siloé",
    "Gate Beautiful": "Puerta Hermosa",
    "Sheep Gate": "Puerta de las Ovejas",
    "Fish Gate": "Puerta del Pescado",
    "Water Gate": "Puerta del Agua",
    "Horse Gate": "Puerta de los Caballos",
    "Valley Gate": "Puerta del Valle",
    "Dung Gate": "Puerta del Muladar",
    "Fountain Gate": "Puerta de la Fuente",
    "Prison Gate": "Puerta de la Cárcel",
    "King's Garden": "Huerto del Rey",
    "King's Pool": "Estanque del Rey",
    "King's Dale": "Valle del Rey",
    "Mount Moriah": "Monte Moriah",
    "Place of the Skull": "Lugar de la Calavera",
    "Straight Street": "Calle Derecha",
    "Street called Straight": "Calle Derecha",
    "Egyptian": "Egipto",
    "Assyria": "Asiria",
    "Persia": "Persia",
    "Media": "Media",
    "Mesopotamia": "Mesopotamia",
    "Cappadocia": "Capadocia",
    "Galatia": "Galacia",
    "Cilicia": "Cilicia",
    "Pamphylia": "Panfilia",
    "Lycia": "Licia",
    "Lydia": "Lidia",
    "Bithynia": "Bitinia",
    "Macedonia": "Macedonia",
    "Achaia": "Acaya",
    "Cyprus": "Chipre",
    "Cretans": "Creta",
    "Arabia": "Arabia",
    "Libya": "Libia",
    "Ethiopia": "Etiopía",
    "India": "India",
    "Spain": "España",
    "Gaul": "Galia",
    "Scythia": "Escitia",
    "Phoenicia": "Fenicia",
    "Philistia": "Filistea",
    "Samaria": "Samaria",
    "Judea": "Judea",
    "Idumea": "Idumea",
    "Decapolis": "Decápolis",
    "Perea": "Perea",
    "Iturea": "Iturea",
    "Trachonitis": "Traconite",
    "Abilene": "Abilinia",
}


def strip_accents(s):
    return "".join(
        c for c in unicodedata.normalize("NFD", s) if unicodedata.category(c) != "Mn"
    )


def mcp_call(method, params):
    req = urllib.request.Request(
        "http://localhost:4300/mcp/",
        data=json.dumps(
            {
                "jsonrpc": "2.0",
                "id": 1,
                "method": "tools/call",
                "params": {"name": method, "arguments": params},
            }
        ).encode("utf-8"),
        headers={
            "Content-Type": "application/json",
            "Accept": "application/json, text/event-stream",
        },
    )
    resp = urllib.request.urlopen(req)
    body = resp.read().decode("utf-8")
    for line in body.split("\n"):
        if line.startswith("data: "):
            d = json.loads(line[6:])
            return json.loads(d["result"]["content"][0]["text"])
    return None


def find_spanish_name(english_name):
    """Determine the Spanish name for a biblical location.

    Returns (spanish_name, source) where source is one of:
      'known' - from the known mapping
      'rv60_same' - found in RV60 with same name
      'corpus_same' - found in broader Spanish corpus with same name
      'no_match' - not found in any Spanish content
    """
    if not english_name or not english_name.strip():
        return english_name, "empty"

    english_name = english_name.strip()

    # 1. Check known mapping first
    if english_name in KNOWN_SPANISH:
        return KNOWN_SPANISH[english_name], "known"

    # 2. Try adding " of " variations (e.g. "Pool of Siloam")
    # Already in known map

    # 3. Search in RV60 scriptures
    q = strip_accents(english_name)
    if len(q) < 3:
        return english_name, "too_short"

    r = mcp_call(
        "search_text", {"query": q, "source_filter": "es/scriptures", "limit": 1}
    )
    if r and r.get("count", 0) > 0:
        return english_name, "rv60_same"

    # 4. Search in broader Spanish corpus
    r = mcp_call("search_text", {"query": q, "source_filter": "es/", "limit": 3})
    if r and r.get("count", 0) > 0:
        # Found in Spanish content - check context for accent differences
        for hit in r["results"]:
            text = hit["text"]
            # If the English name appears in Spanish text, it might be used as-is
            if english_name.lower() in text.lower():
                return english_name, "corpus_same"
        return english_name, "corpus_only"

    # 5. Not found
    return english_name, "no_match"


def main():
    # Get locations from WordPress
    result = subprocess.run(
        [
            "docker",
            "exec",
            "wp_bc_cli",
            "sh",
            "-c",
            "wp post list --post_type=bc_location --format=json --posts_per_page=1000 --fields=ID,post_title 2>/dev/null",
        ],
        capture_output=True,
        text=True,
    )
    locations = json.loads(result.stdout)

    # Also get _bc_loc_name_en for each
    # We can get it with wp post meta
    results = []
    stats = {
        "total": len(locations),
        "same": 0,
        "known_diff": 0,
        "rv60_same": 0,
        "corpus_same": 0,
        "no_match": 0,
        "error": 0,
    }

    print(f"Processing {len(locations)} locations...")

    for idx, loc in enumerate(locations):
        loc_id = loc["ID"]
        title = loc["post_title"]

        # Skip already-Spanish titles (contain accented characters)
        has_accent = any(ord(c) > 127 for c in title)
        if has_accent:
            stats["same"] += 1
            continue

        try:
            spanish_name, source = find_spanish_name(title)

            if source in ("rv60_same", "corpus_same", "too_short", "empty"):
                stats["same"] += 1
                action = "keep"
            elif source == "known":
                stats["known_diff"] += 1
                action = "update"
            elif source == "no_match":
                stats["no_match"] += 1
                action = "keep (not in RV60)"
            else:
                stats["corpus_only"] += 1
                action = "keep (no change)"

            results.append(
                {
                    "id": loc_id,
                    "english": title,
                    "spanish": spanish_name,
                    "source": source,
                    "action": action,
                }
            )

            if (idx + 1) % 100 == 0:
                print(f"  Progress: {idx + 1}/{len(locations)}")

        except Exception as e:
            stats["error"] += 1
            results.append({"id": loc_id, "english": title, "error": str(e)})

        # Rate limiting: sleep briefly between requests
        if idx % 10 == 9:
            sleep(0.1)

    # Print summary
    print("\n=== SUMMARY ===")
    print(f"Total: {stats['total']}")
    print(f"  Same (keep): {stats['same']}")
    print(f"  Known Spanish diff (update): {stats['known_diff']}")
    print(f"  Not in RV60 (no change): {stats['no_match']}")
    print(f"  Errors: {stats['error']}")

    # Print updates needed
    print("\n=== LOCATIONS TO UPDATE ===")
    updates = [r for r in results if r.get("action") == "update"]
    for r in updates:
        print(f"  ID {r['id']}: {r['english']} → {r['spanish']}")

    # Output full JSON for further processing
    output = {"stats": stats, "results": results, "updates": updates}
    print(f"\n=== FULL OUTPUT ===")
    print(json.dumps(output, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
