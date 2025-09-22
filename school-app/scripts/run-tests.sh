#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")"/.. && pwd)"
LOG="$ROOT/storage/logs/install.log"
mkdir -p "$(dirname "$LOG")"
ts() { date '+%Y-%m-%d %H:%M:%S'; }

echo "[$(ts)] TESTS: starting" | tee -a "$LOG"

run_cmd() {
  cmd="$1"; shift || true
  if [ -x "$ROOT/$cmd" ] || command -v "$cmd" >/dev/null 2>&1; then
    (cd "$ROOT" && $cmd "$@")
    return $?
  else
    return 127
  fi
}

status=0
if [ -f "$ROOT/artisan" ]; then
  echo "[$(ts)] Running php artisan test" | tee -a "$LOG"
  if ! (cd "$ROOT" && php artisan test | tee -a "$LOG"); then status=$?; fi
elif [ -f "$ROOT/vendor/bin/phpunit" ]; then
  echo "[$(ts)] Running vendor/bin/phpunit" | tee -a "$LOG"
  if ! (cd "$ROOT" && php "$ROOT/vendor/bin/phpunit" | tee -a "$LOG"); then status=$?; fi
else
  echo "[$(ts)] WARN: No artisan or phpunit found; skipping tests" | tee -a "$LOG"
fi

if [ $status -ne 0 ]; then
  echo "[$(ts)] TESTS: failed with exit $status" | tee -a "$LOG"
  php "$ROOT/scripts/notify.php" "tests.failed" "Tests failed with exit $status" || true
else
  echo "[$(ts)] TESTS: passed" | tee -a "$LOG"
fi

exit $status

