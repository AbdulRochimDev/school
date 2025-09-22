# Testing

- Use `php artisan test` for running the suite.
- Feature tests focus on main flows: Attendance, Submission, Finance, PPDB.
- Unit tests cover policies (RBAC) and domain services.

In shared hosting, use the watcher to re-run tests on file changes and log outputs to `storage/logs/install.log`.

## Watcher
- `scripts/watcher.sh` polls for FS changes and runs `scripts/run-tests.sh` automatically.

## Exit Codes
- Test failures are logged and trigger `scripts/notify.php`.

