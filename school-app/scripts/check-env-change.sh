#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")"/.. && pwd)"
STATE="$ROOT/storage/.env.hash"
ENVF="$ROOT/.env"
if [ ! -f "$ENVF" ]; then exit 0; fi
mkdir -p "$ROOT/storage"
HASH=$(md5sum "$ENVF" | cut -d' ' -f1)
OLD=""
if [ -f "$STATE" ]; then OLD=$(cat "$STATE" || true); fi
if [ "$HASH" != "$OLD" ]; then
  echo "$HASH" > "$STATE"
  php "$ROOT/scripts/notify.php" "env.changed" ".env changed; hash=$HASH" || true
fi

