#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")"/.. && pwd)"
LOG="$ROOT/storage/logs/install.log"
mkdir -p "$(dirname "$LOG")"
ts() { date '+%Y-%m-%d %H:%M:%S'; }

echo "[$(ts)] SETUP: start one-shot" | tee -a "$LOG"

# Pre-change backup
bash "$ROOT/scripts/backup.sh" || true

# Check Laravel installation health
php "$ROOT/scripts/check-laravel.php" | tee -a "$LOG" || true

# Composer install if vendor missing
if [ ! -f "$ROOT/vendor/autoload.php" ] && command -v composer >/dev/null 2>&1; then
  echo "[$(ts)] Composer install --no-dev --optimize-autoloader" | tee -a "$LOG"
  composer install --no-dev --optimize-autoloader | tee -a "$LOG" || true
fi

# Prefer migrations if available
if [ -f "$ROOT/artisan" ]; then
  # Validate env
  php "$ROOT/scripts/validate-env.php" | tee -a "$LOG" || true
  # Ensure .env.testing
  if [ ! -f "$ROOT/.env.testing" ] && [ -f "$ROOT/env/.env.testing.example" ]; then
    cp "$ROOT/env/.env.testing.example" "$ROOT/.env.testing" && echo "[$(ts)] Created .env.testing from example" | tee -a "$LOG"
  fi
  # Ensure app key
  echo "[$(ts)] Running php artisan key:generate --force" | tee -a "$LOG"
  php "$ROOT/artisan" key:generate --force | tee -a "$LOG" || true
  echo "[$(ts)] Running php artisan migrate --force" | tee -a "$LOG"
  php "$ROOT/artisan" migrate --force | tee -a "$LOG" || true
  echo "[$(ts)] Clearing caches (cache/config/route/view)" | tee -a "$LOG"
  php "$ROOT/artisan" cache:clear | tee -a "$LOG" || true
  php "$ROOT/artisan" config:clear | tee -a "$LOG" || true
  php "$ROOT/artisan" route:clear | tee -a "$LOG" || true
  php "$ROOT/artisan" view:clear | tee -a "$LOG" || true
  echo "[$(ts)] Rebuilding caches (config/route/view)" | tee -a "$LOG"
  php "$ROOT/artisan" config:cache | tee -a "$LOG" || true
  php "$ROOT/artisan" route:cache | tee -a "$LOG" || true
  php "$ROOT/artisan" view:cache | tee -a "$LOG" || true
else
  echo "[$(ts)] WARN: artisan not found; will rely on SQL files" | tee -a "$LOG"
fi

# Validate env + run fallback SQL if needed
php "$ROOT/scripts/setup.php" --fallback | tee -a "$LOG"

# Seed database (DatabaseSeeder)
if [ -f "$ROOT/artisan" ]; then
  echo "[$(ts)] Seeding database (DatabaseSeeder)" | tee -a "$LOG"
  php "$ROOT/artisan" db:seed --force | tee -a "$LOG" || true
fi

# Run tests
bash "$ROOT/scripts/run-tests.sh" || true

echo "[$(ts)] SETUP: completed" | tee -a "$LOG"
