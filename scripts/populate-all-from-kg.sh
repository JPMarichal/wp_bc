#!/bin/bash
# populate-all-from-kg.sh — Populate ALL WordPress posts from KG in one batch
#
# Usage: bash scripts/populate-all-from-kg.sh [--dry-run] [limit=N]
#
# Reads all published "persona" posts and runs populate-from-kg.sh for each.
# Use --dry-run to see what would be updated without writing.
# Use limit=N to process only first N posts (for testing).

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
DRY_RUN=false
LIMIT=""

for arg in "$@"; do
    case "$arg" in
        --dry-run) DRY_RUN=true ;;
        limit=*) LIMIT="${arg#limit=}" ;;
    esac
done

# Get all published persona posts
POSTS=$(wp post list --post_type=persona --post_status=publish --format=ids 2>/dev/null)
TOTAL=$(echo "$POSTS" | wc -w)
echo "Found $TOTAL published persona posts"

if [ -n "$LIMIT" ] && [ "$LIMIT" -lt "$TOTAL" ]; then
    POSTS=$(echo "$POSTS" | tr ' ' '\n' | head -"$LIMIT")
    echo "Processing first $LIMIT (limited by limit=$LIMIT)"
fi

COUNT=0
SUCCESS=0
FAIL=0
SKIP=0

for POST_ID in $POSTS; do
    COUNT=$((COUNT + 1))
    POST_TITLE=$(wp post get "$POST_ID" --field=post_title 2>/dev/null || echo "???")
    echo "[$COUNT/$TOTAL] $POST_TITLE (ID=$POST_ID)..."

    if [ "$DRY_RUN" = true ]; then
        # In dry-run mode, just check if KG has the entity
        KG_CHECK=$(curl -s -X POST "http://localhost:4300/search/graph/find" \
            -H "Content-Type: application/json" \
            -d "{\"query\": \"$POST_TITLE\", \"limit\": 3}" 2>/dev/null | \
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
" 2>/dev/null)
        if [ -n "$KG_CHECK" ]; then
            echo "  -> KG OK: $KG_CHECK"
            SUCCESS=$((SUCCESS + 1))
        else
            echo "  -> NO KG ENTITY"
            SKIP=$((SKIP + 1))
        fi
    else
        if bash "$SCRIPT_DIR/populate-from-kg.sh" "$POST_ID" 2>&1; then
            SUCCESS=$((SUCCESS + 1))
        else
            echo "  FAILED"
            FAIL=$((FAIL + 1))
        fi
    fi
done

echo ""
echo "=== Summary ==="
echo "Total: $COUNT"
echo "Success: $SUCCESS"
echo "Failed: $FAIL"
echo "Skipped: $SKIP"
