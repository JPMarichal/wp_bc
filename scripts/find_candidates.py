import sys, re

with open(
    "C:/own/wp_bc/corpus/biographical-encyclopedia/vol1.txt",
    "r",
    encoding="utf-8",
    errors="replace",
) as f:
    lines = f.readlines()

entry_header_re = re.compile(r"^([A-Z][A-Z\']{2,20}),\s{2,}([A-Z][a-z])")

# Find entries for different patterns
results = {"mother": [], "father": [], "parents": [], "son_of": []}

for i, line in enumerate(lines):
    # Mother pattern: "his mother, Name"
    if re.search(r"[Hh]is\s+mother,\s{2,}[A-Z]", line):
        for j in range(i, max(i - 50, -1), -1):
            m = entry_header_re.match(lines[j])
            if m:
                surname = m.group(1)
                rest = lines[j][m.end() + 1 :].strip().rstrip(",").split(",")[0].strip()
                given_parts = rest.split()
                given_rg = r"\s+".join(re.escape(p) for p in given_parts)
                entry_re = re.compile(
                    r"^" + re.escape(surname) + r",\s{2,}" + given_rg + r","
                )
                if entry_re.match(lines[j]):
                    results["mother"].append((surname, rest, i, j))
                break

    # Father pattern: "His father, Name" or "his father, Name"
    if re.search(r"[Hh]is\s+father,\s{2,}[A-Z]", line):
        for j in range(i, max(i - 50, -1), -1):
            m = entry_header_re.match(lines[j])
            if m:
                surname = m.group(1)
                rest = lines[j][m.end() + 1 :].strip().rstrip(",").split(",")[0].strip()
                given_parts = rest.split()
                given_rg = r"\s+".join(re.escape(p) for p in given_parts)
                entry_re = re.compile(
                    r"^" + re.escape(surname) + r",\s{2,}" + given_rg + r","
                )
                if entry_re.match(lines[j]):
                    results["father"].append((surname, rest, i, j))
                break

    # Parents pattern: "His parents, Name and Name"
    if re.search(r"[Hh]is\s+parents,\s{2,}[A-Z]", line):
        for j in range(i, max(i - 50, -1), -1):
            m = entry_header_re.match(lines[j])
            if m:
                surname = m.group(1)
                rest = lines[j][m.end() + 1 :].strip().rstrip(",").split(",")[0].strip()
                given_parts = rest.split()
                given_rg = r"\s+".join(re.escape(p) for p in given_parts)
                entry_re = re.compile(
                    r"^" + re.escape(surname) + r",\s{2,}" + given_rg + r","
                )
                if entry_re.match(lines[j]):
                    results["parents"].append((surname, rest, i, j))
                break

    # Son_of pattern: "...was the son of" or "...is the son of"
    if re.search(r"\b(?:was|is)\s+(?:the\s+)?(?:\w*\s+)?son\s+of\s+[A-Z]", line):
        for j in range(i, max(i - 50, -1), -1):
            m = entry_header_re.match(lines[j])
            if m:
                surname = m.group(1)
                rest = lines[j][m.end() + 1 :].strip().rstrip(",").split(",")[0].strip()
                given_parts = rest.split()
                given_rg = r"\s+".join(re.escape(p) for p in given_parts)
                entry_re = re.compile(
                    r"^" + re.escape(surname) + r",\s{2,}" + given_rg + r","
                )
                if entry_re.match(lines[j]):
                    results["son_of"].append((surname, rest, i, j))
                break

for pat, entries in results.items():
    print(f"\n=== {pat.upper()} ({len(entries)} found) ===")
    for surname, given, match_line, header_line in entries[:15]:
        print(f"  {surname}, {given}  (header={header_line}, match={match_line})")
