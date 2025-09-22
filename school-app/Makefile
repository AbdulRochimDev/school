SHELL := /bin/bash

.PHONY: install setup test watch backup validate

install:
	@if [ ! -f vendor/autoload.php ]; then \
		if command -v composer >/dev/null 2>&1; then \
			composer install --no-dev --optimize-autoloader; \
		else \
			echo "composer not found"; exit 1; \
		fi; \
	fi

setup:
	php scripts/check-laravel.php || true
	bash scripts/setup-one-shot.sh

test:
	bash scripts/run-tests.sh

watch:
	bash scripts/watcher.sh

backup:
	bash scripts/backup.sh

validate:
	php scripts/validate-env.php

