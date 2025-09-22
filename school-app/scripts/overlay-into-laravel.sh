#!/usr/bin/env bash
set -euo pipefail

if [ $# -lt 1 ]; then
  echo "Usage: $0 /absolute/path/to/laravel-root" >&2
  exit 2
fi

SRC="$(cd "$(dirname "$0")"/.. && pwd)"
DEST="$1"

if [ ! -d "$DEST" ] || [ ! -f "$DEST/artisan" ]; then
  echo "Destination does not look like a Laravel app: $DEST" >&2
  exit 3
fi

LOG="$DEST/storage/logs/install.log"
mkdir -p "$(dirname "$LOG")"
ts() { date '+%Y-%m-%d %H:%M:%S'; }

echo "[$(ts)] OVERLAY: start -> $DEST" | tee -a "$LOG"

copy_dir() {
  local rel="$1"
  if [ -d "$SRC/$rel" ]; then
    mkdir -p "$DEST/$rel"
    rsync -a --delete-after "$SRC/$rel/" "$DEST/$rel/"
    echo "[$(ts)] OVERLAY: copied $rel" | tee -a "$LOG"
  fi
}

copy_file() {
  local rel="$1"
  if [ -f "$SRC/$rel" ]; then
    mkdir -p "$(dirname "$DEST/$rel")"
    cp -f "$SRC/$rel" "$DEST/$rel"
    echo "[$(ts)] OVERLAY: copied $rel" | tee -a "$LOG"
  fi
}

# Copy prepared components
copy_dir app
copy_dir routes
copy_dir database
copy_dir scripts
copy_dir docs
copy_dir env
copy_file GUIDE-LARAVEL-NAMECHEAP.md
copy_file Makefile

echo "[$(ts)] OVERLAY: completed" | tee -a "$LOG"

echo "Next steps:" | tee -a "$LOG"
echo "  1) cd $DEST" | tee -a "$LOG"
echo "  2) composer install --no-dev --optimize-autoloader" | tee -a "$LOG"
echo "  3) cp env/.env.namecheap.example .env && php artisan key:generate --force" | tee -a "$LOG"
echo "  4) bash scripts/setup-one-shot.sh" | tee -a "$LOG"

