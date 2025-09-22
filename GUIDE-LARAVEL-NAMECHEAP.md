# Laravel on Namecheap (cPanel) – Guide

This project is optimized for Namecheap shared hosting. Follow these steps for a clean, idempotent install.

## 1) Prepare Hosting
- Create subdomain or use main domain.
- Set Document Root to `public/` inside the project (e.g., `/home/CPANELUSER/apps/school/public`).
- Create MySQL database and user in cPanel:
  - Database: `cpaneluser_school`
  - User: `cpaneluser_dbuser` with strong password
  - Grant ALL PRIVILEGES on the DB.

## 2) Deploy Code
- Via cPanel File Manager or `git clone`/SFTP to e.g. `/home/CPANELUSER/apps/school`.
- Ensure the project contains the `artisan` file and `vendor/` (if not, run composer in step 3).

## 3) Composer Install
- Open cPanel Terminal or SSH.
- cd into project directory: `cd ~/apps/school`
- Run: `composer install --no-dev --optimize-autoloader`

If composer is unavailable, use Namecheap’s “Setup Node.js/Composer” app or upload vendor/ from local.

## 4) Environment
- Copy example env: `cp env/.env.namecheap.example .env`
- Edit `.env` and set:
  - `APP_URL`, `APP_KEY` (generate below)
  - `DB_*` (DB_HOST must be `localhost`)
- Generate app key: `php artisan key:generate --force`

## 5) Permissions
- Ensure `storage/` and `bootstrap/cache/` are writable:
  - `chmod -R 775 storage bootstrap/cache`

## 6) Database Schema & Migrations
- One‑shot setup (validates .env; runs SQL; migrates; tests):
  - `bash scripts/setup-one-shot.sh`
- Or manually:
  - `php scripts/setup.php`
  - `php artisan migrate --force`

The setup runner executes in order and is idempotent:
- `database/mysql/initial_schema.sql` (if present)
- `database/mysql/sample_seed.sql` (if present)
- `database/mysql/attendance_additions.sql`
- `database/mysql/rbac_seed.sql`
- `docs/DATABASE-SCHEMA.sql` (if present)

## 7) Routes
- API: `/api` prefix; health at `/api/health`.
- Teacher & Student APIs gated by `auth:sanctum` and `Gate` roles.

## 8) Watcher & Automation
- Run continuous tests (optional, via SSH):
  - `bash scripts/watcher.sh`
- Cron suggestions (optional):
  - Every 5 minutes tests: `*/5 * * * * /bin/bash /home/CPANELUSER/apps/school/scripts/run-tests.sh >> /dev/null 2>&1`
  - Nightly backup: `0 3 * * * /bin/bash /home/CPANELUSER/apps/school/scripts/backup.sh`

## 9) Notifications
- Test failures and env changes log to `storage/logs/install.log` and attempt DB insert into `notifications` if table exists.

## 10) Troubleshooting
- 500 error: check `storage/logs/laravel.log` and `storage/logs/install.log`.
- DB connection: ensure `DB_HOST=localhost` and cPanel prefixes match between DB and user.
- Missing vendor: re‑run composer install.

## 11) Security
- Disable debug in production: `APP_DEBUG=false`.
- Use HTTPS.
- Keep permissions minimal; avoid world‑writable.

