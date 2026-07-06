#!/bin/bash
BACKUP_DIR="/var/www/html/backups"
LATEST="$BACKUP_DIR/db-latest.sql"
PREVIOUS="$BACKUP_DIR/db-previous.sql"

mkdir -p "$BACKUP_DIR"

[ -f "$LATEST" ] && cp "$LATEST" "$PREVIOUS"

wp db export "$LATEST" --allow-root
