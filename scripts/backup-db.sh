#!/bin/sh
set -e

# backup-db.sh: Exporta la base de datos a wp-content/uploads/db-backups/
# como respaldo adicional al volumen persistente db-data/.
#
# Uso: scripts/backup-db.sh [filename]

CONTAINER="wp_bc_cli"
BACKUP_DIR="/var/www/html/wp-content/uploads/db-backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
FILENAME="${1:-bc_wp_$TIMESTAMP.sql}"

echo "=== Respaldando base de datos ==="

# Crear directorio si no existe
docker exec "$CONTAINER" mkdir -p "$BACKUP_DIR" 2>/dev/null

# Exportar
MSYS_NO_PATHCONV=1 docker exec "$CONTAINER" wp db export "$BACKUP_DIR/$FILENAME" --allow-root

echo "  ✓ Respaldado: wp-content/uploads/db-backups/$FILENAME"
echo "=== Listo ==="
