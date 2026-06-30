#!/bin/sh
# Database & options maintenance for wp_bc via WP-CLI
# Usage: docker exec wp_bc_cli sh /var/www/html/bin/wp-maintenance.sh

echo "=== 1. Expired transients ==="
wp transient delete --expired

echo ""
echo "=== 2. Spam comments ==="
wp comment delete $(wp comment list --status=spam --format=ids) --force 2>/dev/null || echo "No spam."

echo ""
echo "=== 3. Old auto-drafts ==="
wp post delete $(wp post list --post_status=auto-draft --format=ids) --force 2>/dev/null || echo "No auto-drafts."

echo ""
echo "=== 4. Optimize DB tables ==="
wp db optimize

echo ""
echo "=== Done ==="
