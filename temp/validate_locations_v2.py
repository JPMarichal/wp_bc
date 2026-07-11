"""Validate location names against RV60 Spanish scriptures via Alejandría.
Reads locations from stdin JSON, outputs validation report to stdout."""

import json
import sys
import unicodedata
import urllib.request
from time import sleep

# Known Spanish equivalents (pre-validated)
KNOWN_SPANISH = {
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
    "Red Sea": "Mar Rojo",
    "Dead Sea": "Mar Muerto",
    "Mount Sinai": "Monte Sinaí",
    "Mount Zion": "Monte Sion",
    "Mount of Olives": "Monte de los Olivos",
    "Pool of Bethesda": "Estanque de Betesda",
    "Thessalonica": "Tesalónica",
    "Macedonia": "Macedonia",
    "Achaia": "Acaya",
    "Cappadocia": "Capadocia",
    "Galatia": "Galacia",
    "Bithynia": "Bitinia",
    "Pamphylia": "Panfilia",
    "Lycaonia": "Licaonia",
    "Phrygia": "Frigia",
    "Cilicia": "Cilicia",
    "Phoenicia": "Fenicia",
    "Philistia": "Filistea",
    "Mesopotamia": "Mesopotamia",
    "Assyria": "Asiria",
    "Persia": "Persia",
    "Media": "Media",
    "Libya": "Libia",
    "Ethiopia": "Etiopía",
    "Arabia": "Arabia",
    "India": "India",
    "Spain": "España",
    "Gaul": "Galia",
    "Scythia": "Escitia",
    "Illyricum": "Ilírico",
    "Dalmatia": "Dalmacia",
    "Cauda": "Clauda",
    "Troas": "Tróade",
    "Attalia": "Atalía",
    "Ptolemais": "Tolemaida",
    "Seleucia": "Seleucia",
    "Salamis": "Salamina",
    "Paphos": "Pafos",
    "Samos": "Samos",
    "Chios": "Quíos",
    "Cos": "Cos",
    "Rhodes": "Rodas",
    "Malta": "Malta",
    "Cyrene": "Cirene",
    "Syracuse": "Siracusa",
    "Puteoli": "Puteoli",
    "Appii Forum": "Foro de Apio",
    "Three Taverns": "Tres Tabernas",
    "Alexandria": "Alejandría",
    "Antioch": "Antioquía",
    "Tarsus": "Tarso",
    "Derbe": "Derbe",
    "Lystra": "Listra",
    "Iconium": "Iconio",
    "Pergamum": "Pérgamo",
    "Thyatira": "Tiatira",
    "Sardis": "Sardis",
    "Philadelphia": "Filadelfia",
    "Laodicea": "Laodicea",
    "Colossae": "Colosas",
    "Hierapolis": "Hierápolis",
    "Miletus": "Mileto",
    "Patmos": "Patmos",
    "Cnidos": "Cnido",
    "Fair Havens": "Puertos Hermosos",
    "Phoenix": "Fénix",
    "Lasea": "Lasea",
    "Adramyttium": "Adramitio",
    "Myra": "Mira",
    "Rhegium": "Regio",
    "Salmone": "Salmón",
    "Assos": "Asón",
    "Mitylene": "Mitilene",
    "Neapolis": "Neápolis",
    "Philippi": "Filipos",
    "Amphipolis": "Anfípolis",
    "Apollonia": "Apollonia",
    "Berea": "Berea",
    "Athens": "Atenas",
    "Corinth": "Corinto",
    "Cenchrea": "Cencrea",
    "Nicopolis": "Nicópolis",
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


def main():
    locations = json.loads(sys.stdin.read())
    results = []
    stats = {
        "total": len(locations),
        "already_spanish": 0,
        "known_diff": 0,
        "rv60_same": 0,
        "no_match": 0,
        "error": 0,
    }

    for idx, loc in enumerate(locations):
        loc_id = loc["ID"]
        title = loc["post_title"].strip()

        # Skip already-Spanish (has accents or contains Spanish-only words)
        has_accent = any(ord(c) > 191 for c in title)
        if has_accent:
            stats["already_spanish"] += 1
            results.append(
                {
                    "id": loc_id,
                    "title": title,
                    "action": "keep (already ES)",
                    "spanish": title,
                }
            )
            continue

        # Check known mapping
        if title in KNOWN_SPANISH:
            es_name = KNOWN_SPANISH[title]
            if es_name != title:
                stats["known_diff"] += 1
                results.append(
                    {
                        "id": loc_id,
                        "title": title,
                        "action": "update",
                        "spanish": es_name,
                        "source": "known",
                    }
                )
            else:
                stats["rv60_same"] += 1
                results.append(
                    {
                        "id": loc_id,
                        "title": title,
                        "action": "keep",
                        "spanish": title,
                        "source": "known_same",
                    }
                )
            continue

        # Search in RV60
        q = strip_accents(title)
        if len(q) >= 3:
            r = mcp_call(
                "search_text",
                {"query": q, "source_filter": "es/scriptures", "limit": 1},
            )
            if r and r.get("count", 0) > 0:
                stats["rv60_same"] += 1
                results.append(
                    {
                        "id": loc_id,
                        "title": title,
                        "action": "keep",
                        "spanish": title,
                        "source": "rv60",
                    }
                )
            else:
                stats["no_match"] += 1
                results.append(
                    {
                        "id": loc_id,
                        "title": title,
                        "action": "unknown",
                        "spanish": title,
                        "source": "not_in_rv60",
                    }
                )
        else:
            stats["rv60_same"] += 1
            results.append(
                {
                    "id": loc_id,
                    "title": title,
                    "action": "keep",
                    "spanish": title,
                    "source": "too_short",
                }
            )

        if (idx + 1) % 100 == 0:
            print(f"  [{idx + 1}/{len(locations)}]", file=sys.stderr)

    # Output results
    output = {"stats": stats, "results": results}
    print(json.dumps(output, ensure_ascii=False, indent=2))

    # Summary to stderr
    print("\n=== SUMMARY ===", file=sys.stderr)
    for k, v in stats.items():
        print(f"  {k}: {v}", file=sys.stderr)
    updates = [r for r in results if r.get("action") == "update"]
    if updates:
        print(f"\n=== TO UPDATE ({len(updates)}) ===", file=sys.stderr)
        for r in updates:
            print(f"  ID {r['id']}: {r['title']} → {r['spanish']}", file=sys.stderr)
    unknowns = [r for r in results if r.get("source") == "not_in_rv60"]
    if unknowns:
        print(
            f"\n=== NOT IN RV60 ({len(unknowns)}) — needs manual review ===",
            file=sys.stderr,
        )
        for r in unknowns[:30]:
            print(f"  ID {r['id']}: {r['title']}", file=sys.stderr)
        if len(unknowns) > 30:
            print(f"  ... and {len(unknowns) - 30} more", file=sys.stderr)


if __name__ == "__main__":
    main()
