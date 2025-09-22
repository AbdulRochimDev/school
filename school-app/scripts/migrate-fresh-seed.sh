#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")"/.. && pwd)"
LOG="$ROOT/storage/logs/install.log"
mkdir -p "$(dirname "$LOG")"
ts() { date '+%Y-%m-%d %H:%M:%S'; }

if [ ! -f "$ROOT/artisan" ]; then
  echo "[$(ts)] FRESH: artisan not found" | tee -a "$LOG";
  exit 1
fi

echo "[$(ts)] FRESH: migrate:fresh --seed" | tee -a "$LOG"
php "$ROOT/artisan" migrate:fresh --seed --force | tee -a "$LOG"
echo "[$(ts)] FRESH: tests" | tee -a "$LOG"
bash "$ROOT/scripts/run-tests.sh"

