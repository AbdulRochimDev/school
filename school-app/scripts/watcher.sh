#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")"/.. && pwd)"
LOG="$ROOT/storage/logs/install.log"
mkdir -p "$(dirname "$LOG")"
ts() { date '+%Y-%m-%d %H:%M:%S'; }

echo "[$(ts)] WATCHER: starting (polling)" | tee -a "$LOG"

checksum() {
  (cd "$ROOT" && find . -type f \
    ! -path './vendor/*' ! -path './storage/*' ! -path './.git/*' ! -path './node_modules/*' \
    -printf '%P %TY%Tm%Td%TH%TM%TS\n' 2>/dev/null | sort | sha1sum | cut -d' ' -f1)
}

prev=""
interval="${WATCH_INTERVAL:-3}"
while true; do
  cur=$(checksum || echo "none")
  if [ "$cur" != "$prev" ]; then
    echo "[$(ts)] WATCHER: change detected -> running tests" | tee -a "$LOG"
    bash "$ROOT/scripts/check-env-change.sh" || true
    bash "$ROOT/scripts/run-tests.sh" || true
    prev="$cur"
  fi
  sleep "$interval"
done
