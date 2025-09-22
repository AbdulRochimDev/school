#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")"/.. && pwd)"
OUTDIR="$ROOT/storage/backups"
mkdir -p "$OUTDIR"
TS=$(date '+%Y%m%d-%H%M%S')

LOG="$ROOT/storage/logs/install.log"
mkdir -p "$(dirname "$LOG")"
ts() { date '+%Y-%m-%d %H:%M:%S'; }

echo "[$(ts)] BACKUP: starting" | tee -a "$LOG"

ENVF="$ROOT/.env"
DB_HOST=""; DB_DATABASE=""; DB_USERNAME=""; DB_PASSWORD=""; DB_PORT="3306"
if [ -f "$ENVF" ]; then
  DB_HOST=$(grep -E '^DB_HOST=' "$ENVF" | head -n1 | cut -d= -f2- | tr -d '"') || true
  DB_DATABASE=$(grep -E '^DB_DATABASE=' "$ENVF" | head -n1 | cut -d= -f2- | tr -d '"') || true
  DB_USERNAME=$(grep -E '^DB_USERNAME=' "$ENVF" | head -n1 | cut -d= -f2- | tr -d '"') || true
  DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "$ENVF" | head -n1 | cut -d= -f2- | tr -d '"') || true
  DB_PORT=$(grep -E '^DB_PORT=' "$ENVF" | head -n1 | cut -d= -f2- | tr -d '"') || echo "3306"
fi

# DB dump if mysqldump exists and credentials present
SQL_DUMP=""
if command -v mysqldump >/dev/null 2>&1 && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
  SQL_DUMP="$OUTDIR/db-$TS.sql.gz"
  echo "[$(ts)] BACKUP: dumping MySQL $DB_DATABASE" | tee -a "$LOG"
  # shellcheck disable=SC2086
  mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_DATABASE" 2>>"$LOG" | gzip -9 > "$SQL_DUMP" || {
    echo "[$(ts)] BACKUP: mysqldump failed; continuing without DB dump" | tee -a "$LOG" ; SQL_DUMP="" ;
  }
else
  echo "[$(ts)] BACKUP: mysqldump not available or DB creds missing; skipping DB dump" | tee -a "$LOG"
fi

FILES_ARC="$OUTDIR/files-$TS.tar.gz"
tar -czf "$FILES_ARC" -C "$ROOT" .env storage 2>/dev/null || true

echo "[$(ts)] BACKUP: created files archive $FILES_ARC" | tee -a "$LOG"
if [ -n "$SQL_DUMP" ]; then echo "[$(ts)] BACKUP: created DB dump $SQL_DUMP" | tee -a "$LOG"; fi
echo "[$(ts)] BACKUP: completed" | tee -a "$LOG"
