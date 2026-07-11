"""Efficient location validation. Strategy:
1. Skip already-Spanish titles (accented chars)
2. Match against comprehensive known ES mapping
3. Only search RV60 for remaining names NOT in mapping
4. For names already valid in Spanish (without accents), keep as-is
5. Report actionable changes
"""

import json, sys, unicodedata, urllib.request

KNOWN_SPANISH = {
    # Major name differences
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
    "Euphrates": "Éufrates",
    "Tigris": "Tigris",
    "Nile": "Nilo",
    "Cherith": "Querit",
    "Kidron": "Cedrón",
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
    "Ethiopia": "Etiopía",
    "Libya": "Libia",
    "Scythia": "Escitia",
    "Illyricum": "Ilírico",
    "Dalmatia": "Dalmacia",
    "Cauda": "Clauda",
    "Troas": "Tróade",
    "Attalia": "Atalía",
    "Ptolemais": "Tolemaida",
    "Salamis": "Salamina",
    "Paphos": "Pafos",
    "Chios": "Quíos",
    "Rhodes": "Rodas",
    "Cyrene": "Cirene",
    "Syracuse": "Siracusa",
    "Three Taverns": "Tres Tabernas",
    "Alexandria": "Alejandría",
    "Tarsus": "Tarso",
    "Lystra": "Listra",
    "Iconium": "Iconio",
    "Pergamum": "Pérgamo",
    "Thyatira": "Tiatira",
    "Philadelphia": "Filadelfia",
    "Laodicea": "Laodicea",
    "Colossae": "Colosas",
    "Hierapolis": "Hierápolis",
    "Miletus": "Mileto",
    "Patmos": "Patmos",
    "Fair Havens": "Puertos Hermosos",
    "Adramyttium": "Adramitio",
    "Myra": "Mira",
    "Rhegium": "Regio",
    "Mitylene": "Mitilene",
    "Neapolis": "Neápolis",
    "Philippi": "Filipos",
    "Amphipolis": "Anfípolis",
    "Berea": "Berea",
    "Athens": "Atenas",
    "Cenchrea": "Cencrea",
    "Nicopolis": "Nicópolis",
    "Derbe": "Derbe",
    "Samos": "Samos",
    "Cos": "Cos",
    "Malta": "Malta",
    "Spain": "España",
    "Greece": "Grecia",
    "Syria": "Siria",
    "Canaan": "Canaán",
    "Arabia": "Arabia",
    "Persia": "Persia",
    "Media": "Media",
    "India": "India",
    "Gaul": "Galia",
    "Sparta": "Esparta",
    "Thebes": "Tebas",
    "Erech": "Erec",
    "Calah": "Cala",
    "Gozan": "Gozán",
    "Habor": "Habor",
    "Pithom": "Pitón",
    "Rameses": "Ramsés",
    "Carthage": "Cartago",
    "Puteoli": "Puteoli",
    "Sardis": "Sardis",
    "Lycia": "Licia",
    "Lydia": "Lidia",
    "Mysia": "Misia",
}

# Names already in Spanish (7-bit clean) — no change needed
ALREADY_SPANISH = {
    "Sodoma",
    "Gomorra",
    "Arnon",
    "Damasco",
    "Corinto",
    "Chipre",
    "Creta",
    "Arabia",
    "Samaria",
    "Judea",
    "Siria",
    "Egipto",
    "Asiria",
    "Babilonia",
    "Persia",
    "Media",
    "India",
    "España",
    "Libia",
    "Etiopía",
    "Galilea",
    "Macedonia",
    "Acaya",
    "Capadocia",
    "Galacia",
    "Bitinia",
    "Panfilia",
    "Cilicia",
    "Fenicia",
    "Mesopotamia",
    "Filistea",
    "Creta",
    "Rodas",
    "Malta",
    "Samos",
    "Cos",
    "Quíos",
    "Patmos",
    "Pafos",
    "Salamina",
    "Tarso",
    "Listra",
    "Iconio",
    "Pérgamo",
    "Tiatira",
    "Filadelfia",
    "Colosas",
    "Mileto",
    "Nicópolis",
    "Filipos",
    "Anfípolis",
    "Atenas",
    "Troas",
    "Atalía",
    "Cirene",
    "Siracusa",
    "Regio",
    "Mitilene",
    "Neápolis",
    "Cencrea",
    "Adramitio",
    "Tolemaida",
    "Berea",
    "Alejandría",
    "Antioquía",
    "Dalmacia",
    "Escitia",
    "Ilírico",
    "Clauda",
    "Tebas",
    "Erec",
    "Cala",
    "Pitón",
    "Ramsés",
    "Goshen",
    "Canaán",
    "Ur",
    "Pisga",
    "Abarim",
    "Nebo",
    "Peniel",
    "Sela",
    "Rimmon",
    "Hesbón",
    "Sibma",
    "Jazer",
    "Eleale",
    "Betsaida",
    "Dan",
    "Betel",
    "Hai",
    "Gabaa",
    "Gabaón",
    "Gaza",
    "Asdod",
    "Ascalón",
    "Ecrón",
    "Get",
    "Ramá",
    "Mizpa",
    "Siló",
    "Siquem",
    "Tirsa",
    "Samaria",
    "Jope",
    "Cesarea",
    "Tiberias",
    "Gerasa",
    "Gadara",
    "Alejandría",
    "Puteoli",
    "Lasea",
    "Fénix",
    "Cesarea",
    "Tiro",
    "Sidón",
    "Jaboc",
    "Arnón",
    "Sorec",
    "Ela",
    "Gerar",
    "Beerseba",
    "Cades",
    "Gosén",
    "Ramés",
    "Sucot",
    "Etam",
    "Migdol",
    "Marah",
    "Elim",
    "Sin",
    "Sinaí",
    "Horeb",
    "Cades-barnea",
    "Arad",
    "Horma",
    "Obot",
    "Jebe",
    "Dibón",
    "Jahaza",
    "Jesbón",
    "Basán",
    "Edrei",
    "Galaad",
    "Jabes",
    "Siquem",
    "Siló",
    "Timnat",
    "Bet-horón",
    "Ajalón",
    "Meguido",
    "Sarid",
    "Cesarea de Filipo",
    "Jope",
    "Lida",
    "Emaús",
    "Betania",
    "Betfagé",
    "Getsemaní",
    "Gólgota",
    "Calvario",
    "Arimatea",
    "Canaán",
    "Genesaret",
    "Decápolis",
    "Perea",
    "Iturea",
    "Traconite",
    "Abilinia",
    "Judea",
    "Samaria",
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
    for line in body.split(chr(10)):
        if line.startswith("data: "):
            d = json.loads(line[6:])
            return json.loads(d["result"]["content"][0]["text"])
    return None


def is_valid_spanish(title):
    """Check if title is already a valid Spanish name."""
    # Base name (remove trailing numbers like "2", "3")
    base = title.split()
    name = base[0] if base else title
    if name in ALREADY_SPANISH:
        return True
    return False


def main():
    locations = json.loads(sys.stdin.read())
    results = []
    stats = {
        "total": len(locations),
        "already_es_accents": 0,
        "already_es_base": 0,
        "known_diff": 0,
        "rv60_same": 0,
        "not_found": 0,
        "api_calls": 0,
    }

    for idx, loc in enumerate(locations):
        loc_id = loc["ID"]
        title = loc["post_title"].strip()

        # 1. Already Spanish (has accents)
        if any(ord(c) > 191 for c in title):
            stats["already_es_accents"] += 1
            continue

        # 2. Already Spanish (base name matches)
        if is_valid_spanish(title):
            stats["already_es_base"] += 1
            continue

        # 3. Known mapping
        if title in KNOWN_SPANISH:
            es = KNOWN_SPANISH[title]
            if es != title:
                stats["known_diff"] += 1
                results.append(
                    {"id": loc_id, "title": title, "action": "update", "spanish": es}
                )
            else:
                stats["rv60_same"] += 1
            continue

        # 4. Check if title without number suffix is in known mapping
        parts = title.rsplit(" ", 1)
        if len(parts) == 2 and parts[1].isdigit():
            base_name = parts[0]
            if base_name in KNOWN_SPANISH:
                es = KNOWN_SPANISH[base_name]
                if es != base_name:
                    stats["known_diff"] += 1
                    results.append(
                        {
                            "id": loc_id,
                            "title": title,
                            "action": "update",
                            "spanish": es + " " + parts[1],
                        }
                    )
                    continue

        # 5. Search in RV60
        q = strip_accents(title.split()[0])  # Search base name only
        if len(q) >= 3:
            r = mcp_call("search_text", {"query": q, "limit": 1})
            stats["api_calls"] += 1
            if r and r.get("count", 0) > 0:
                stats["rv60_same"] += 1
            else:
                stats["not_found"] += 1
                results.append(
                    {
                        "id": loc_id,
                        "title": title,
                        "action": "review",
                        "spanish": title,
                        "note": "not in corpus",
                    }
                )
        else:
            stats["rv60_same"] += 1

        if (idx + 1) % 50 == 0:
            print(
                f"[{idx + 1}/{len(locations)}] api_calls={stats['api_calls']}",
                file=sys.stderr,
            )

    print(
        json.dumps({"stats": stats, "results": results}, ensure_ascii=False, indent=2)
    )

    print("\n=== SUMMARY ===", file=sys.stderr)
    for k, v in stats.items():
        print(f"  {k}: {v}", file=sys.stderr)
    updates = [r for r in results if r.get("action") == "update"]
    if updates:
        print(f"\n=== UPDATES ({len(updates)}) ===", file=sys.stderr)
        for r in updates:
            print(f"  ID {r['id']}: {r['title']} → {r['spanish']}", file=sys.stderr)
    reviews = [r for r in results if r.get("action") == "review"]
    if reviews:
        print(f"\n=== FOR REVIEW ({len(reviews)}) ===", file=sys.stderr)
        for r in reviews[:30]:
            print(
                f"  ID {r['id']}: {r['title']} ({r.get('note', '')})", file=sys.stderr
            )


if __name__ == "__main__":
    main()
