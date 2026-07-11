import json

with open("/tmp/result.json") as f:
    data = json.load(f)
reviews = [r["title"] for r in data["results"] if r.get("action") == "review"]
print("Total review items:", len(reviews))
print()

from collections import Counter

spanish_indicators = [
    "valle",
    "monte",
    "mar",
    "rio",
    "arroyo",
    "puertos",
    "desierto",
    "camino",
    "aguas",
    "calle",
    "templo",
    "colina",
    "gran",
    "roca",
    "rocas",
    "sitio",
    "fortaleza",
    "puerta",
    "laguna",
    "llanura",
    "pozo",
    "torre",
    "fuente",
    "campo",
    "ciudad",
    "palacio",
    "casa",
    "tierra",
    "rey",
    "reina",
    "lower",
    "upper",
]

print("=== Items that look like English ===")
likely_english = []
for t in reviews:
    first_word = t.split()[0].lower()
    if first_word in spanish_indicators:
        continue
    words = t.lower().split()
    if any(w in spanish_indicators for w in words):
        continue
    if "(" in t or ")" in t:
        continue
    likely_english.append(t)
print(f"Count: {len(likely_english)}")
for t in likely_english[:80]:
    print(f"  {t}")
if len(likely_english) > 80:
    print(f"  ... and {len(likely_english) - 80} more")

print()
print("=== Items with (parentheses) ===")
paren = [t for t in reviews if "(" in t]
print(f"Count: {len(paren)}")
for t in paren:
    print(f"  {t}")

print()
print("=== Items starting with Spanish words ===")
sw = [t for t in reviews if t.split()[0].lower() in spanish_indicators]
print(f"Count: {len(sw)}")
for t in sw:
    print(f"  {t}")
