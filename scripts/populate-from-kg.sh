#!/bin/bash
# populate-from-kg.sh — Populate WordPress post meta from Alejandría KG
#
# Usage: bash scripts/populate-from-kg.sh <post_id_or_slug>
#
# Reads:
#   1. KG API (relations: spouse, family, callings, birth/death places, nationality)
#   2. bridge-data.json (dates, images)
# Writes WordPress post meta via `wp post meta update`

set -euo pipefail

POST_ID="${1:-}"
if [ -z "$POST_ID" ]; then
    echo "Usage: bash scripts/populate-from-kg.sh <post_id_or_slug>"
    exit 1
fi

API_BASE="http://localhost:4300"
SCRIPT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
BRIDGE_DATA="$SCRIPT_DIR/../alejandria/data/kg-seeds/bridge-data.json"

if [ ! -f "$BRIDGE_DATA" ]; then
    echo "ERROR: bridge-data.json not found at $BRIDGE_DATA"
    exit 1
fi

# Resolve post ID from slug if needed
if ! [[ "$POST_ID" =~ ^[0-9]+$ ]]; then
    POST_ID=$(wp post list --name="$POST_ID" --format=ids --post_type=persona 2>/dev/null | head -1)
    if [ -z "$POST_ID" ]; then
        echo "ERROR: Could not resolve slug '$1' to post ID"
        exit 1
    fi
fi

# Get post slug and title
POST_SLUG=$(wp post get "$POST_ID" --field=post_name 2>/dev/null)
POST_TITLE=$(wp post get "$POST_ID" --field=post_title 2>/dev/null)
echo "Populating: $POST_TITLE (ID=$POST_ID, slug=$POST_SLUG)"

# ---- Step 1: Resolve KG entity ----
KG_NAME=$(curl -s -X POST "$API_BASE/search/graph/find" \
    -H "Content-Type: application/json" \
    -d "{\"query\": \"$POST_TITLE\", \"limit\": 5}" | \
    python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    for r in d.get('results', []):
        if r.get('type') == 'person':
            print(r['name'])
            break
except:
    pass
")

if [ -z "$KG_NAME" ]; then
    echo "WARNING: No KG entity found for '$POST_TITLE'"
fi

echo "KG entity: ${KG_NAME:-none}"

# ---- Step 2: Fetch KG relations ----
RELATIONS_JSON=""
if [ -n "$KG_NAME" ]; then
    RELATIONS_JSON=$(curl -s -X POST "$API_BASE/search/graph/relations" \
        -H "Content-Type: application/json" \
        -d "{\"name\": \"$KG_NAME\"}" 2>/dev/null)
fi

# ---- Step 3: Extract data from bridge-data.json ----
BRIDGE_ENTRY=$(python -c "
import sys,json
with open('$BRIDGE_DATA', 'r') as f:
    data = json.load(f)
entry = data.get('$POST_TITLE', {})
if entry:
    print(json.dumps(entry))
" 2>/dev/null || echo "{}")

BIRTH_DATE=$(echo "$BRIDGE_ENTRY" | python -c "import sys,json; d=json.load(sys.stdin); print(d.get('birth_date',''))" 2>/dev/null)
DEATH_DATE=$(echo "$BRIDGE_ENTRY" | python -c "import sys,json; d=json.load(sys.stdin); print(d.get('death_date',''))" 2>/dev/null)
IMAGE=$(echo "$BRIDGE_ENTRY" | python -c "import sys,json; d=json.load(sys.stdin); print(d.get('image',''))" 2>/dev/null)

# ---- Step 4: Parse KG relations and update meta ----
if [ -n "$RELATIONS_JSON" ] && [ "$RELATIONS_JSON" != "null" ]; then
    # Birth place
    BIRTH_PLACE=$(echo "$RELATIONS_JSON" | python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    for r in d.get('relations', []):
        if r.get('rel_type') == 'BORN_IN':
            print(r.get('to_name',''))
            break
except:
    pass
" 2>/dev/null)

    # Death place
    DEATH_PLACE=$(echo "$RELATIONS_JSON" | python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    for r in d.get('relations', []):
        if r.get('rel_type') == 'DIED_IN':
            print(r.get('to_name',''))
            break
except:
    pass
" 2>/dev/null)

    # Nationality
    NATIONALITY=$(echo "$RELATIONS_JSON" | python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    for r in d.get('relations', []):
        if r.get('rel_type') == 'NATIONALITY':
            print(r.get('to_name',''))
            break
except:
    pass
" 2>/dev/null)

    # Spouses
    SPOUSES=$(echo "$RELATIONS_JSON" | python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    spouses = []
    for r in d.get('relations', []):
        if r.get('rel_type') == 'SPOUSE_OF':
            spouses.append({'name': r.get('to_name','')})
    print(json.dumps(spouses))
except:
    print('[]')
" 2>/dev/null)

    # Father
    FATHER=$(echo "$RELATIONS_JSON" | python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    for r in d.get('relations', []):
        if r.get('rel_type') == 'FATHER_OF':
            print(r.get('to_name',''))
            break
except:
    pass
" 2>/dev/null)

    # Mother
    MOTHER=$(echo "$RELATIONS_JSON" | python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    for r in d.get('relations', []):
        if r.get('rel_type') == 'MOTHER_OF':
            print(r.get('to_name',''))
            break
except:
    pass
" 2>/dev/null)

    # Callings
    CALLINGS=$(echo "$RELATIONS_JSON" | python -c "
import sys,json
try:
    d = json.load(sys.stdin)
    callings = []
    for r in d.get('relations', []):
        if r.get('rel_type') == 'CALLED_AS':
            callings.append({
                'calling': r.get('rel_type',''),
                'org': r.get('to_name','')
            })
    print(json.dumps(callings))
except:
    print('[]')
" 2>/dev/null)

    # ---- Step 5: Write post meta ----
    echo ""
    echo "=== Updating meta ==="

    if [ -n "$BIRTH_DATE" ]; then
        wp post meta update "$POST_ID" _author_birth_date "$BIRTH_DATE" 2>/dev/null || true
        echo "  _author_birth_date -> $BIRTH_DATE"
    fi

    if [ -n "$DEATH_DATE" ]; then
        wp post meta update "$POST_ID" _author_death_date "$DEATH_DATE" 2>/dev/null || true
        echo "  _author_death_date -> $DEATH_DATE"
    fi

    if [ -n "$BIRTH_PLACE" ]; then
        wp post meta update "$POST_ID" _author_birth_place "$BIRTH_PLACE" 2>/dev/null || true
        echo "  _author_birth_place -> $BIRTH_PLACE"
    fi

    if [ -n "$DEATH_PLACE" ]; then
        wp post meta update "$POST_ID" _author_death_place "$DEATH_PLACE" 2>/dev/null || true
        echo "  _author_death_place -> $DEATH_PLACE"
    fi

    if [ -n "$NATIONALITY" ]; then
        wp post meta update "$POST_ID" _author_nationality "$NATIONALITY" 2>/dev/null || true
        echo "  _author_nationality -> $NATIONALITY"
    fi

    if [ -n "$FATHER" ]; then
        wp post meta update "$POST_ID" _author_father "$FATHER" 2>/dev/null || true
        echo "  _author_father -> $FATHER"
    fi

    if [ -n "$MOTHER" ]; then
        wp post meta update "$POST_ID" _author_mother "$MOTHER" 2>/dev/null || true
        echo "  _author_mother -> $MOTHER"
    fi

    if [ "$SPOUSES" != "[]" ] && [ -n "$SPOUSES" ]; then
        wp post meta update "$POST_ID" _author_spouses "$SPOUSES" --format=json 2>/dev/null || true
        SPOUSE_COUNT=$(echo "$SPOUSES" | python -c "import sys,json; print(len(json.load(sys.stdin)))" 2>/dev/null)
        echo "  _author_spouses -> $SPOUSE_COUNT spouses"
    fi

    if [ "$CALLINGS" != "[]" ] && [ -n "$CALLINGS" ]; then
        wp post meta update "$POST_ID" _author_callings "$CALLINGS" --format=json 2>/dev/null || true
        CALLING_COUNT=$(echo "$CALLINGS" | python -c "import sys,json; print(len(json.load(sys.stdin)))" 2>/dev/null)
        echo "  _author_callings -> $CALLING_COUNT callings"
    fi

    # Image — download and set as featured
    if [ -n "$IMAGE" ]; then
        echo "  Image URL available: ${IMAGE:0:80}..."
        echo "  (use import-photo.sh for actual download)"
    fi

    echo ""
    echo "Done: $POST_TITLE"
else
    echo "WARNING: No KG relations found for '$POST_TITLE'"
fi
