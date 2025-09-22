# Cron Examples (Namecheap)

Replace CPANELUSER and path accordingly.

- Every 5 min: run tests and log results
```
*/5 * * * * /bin/bash /home/CPANELUSER/apps/school/scripts/run-tests.sh >> /dev/null 2>&1
```

- Nightly backup at 03:00
```
0 3 * * * /bin/bash /home/CPANELUSER/apps/school/scripts/backup.sh >> /dev/null 2>&1
```

- Optional watcher (not recommended under cron; use SSH session)

