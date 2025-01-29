# Run `make` (no arguments) to get a short description of what is available
# within this `Makefile`.

help: ## shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
.PHONY: help

qa: cs sa test deps ## Run all QA checks

test: ## Run tests
	time -p php -d xdebug.mode=off ./vendor/bin/phpunit
.PHONY: test

get-require-checker: ## Download a Phar of composer-require-checker
ifeq (,$(wildcard ./vendor/bin/composer-require-checker))
	curl -LsS https://github.com/maglnet/ComposerRequireChecker/releases/latest/download/composer-require-checker.phar -o vendor/bin/composer-require-checker
	chmod +x vendor/bin/composer-require-checker
endif
.PHONY: get-require-checker

deps: get-require-checker ## Check for un-declared dependencies
	php -d xdebug.mode=off -f vendor/bin/composer-require-checker -- check
.PHONY: deps

bump: ## Bump Composer deps
	composer update
	composer bump --dev-only
	composer update
.PHONY: bump

sa: ## Run static analysis
	php -d xdebug.mode=off vendor/bin/psalm --no-cache
.PHONY: sa

update-baseline: ## Update SA Baseline removing fixed issues
	php -d xdebug.mode=off vendor/bin/psalm --no-cache --update-baseline
.PHONY: update-baseline

set-baseline: ## Baseline outstanding SA Issues
	php -d xdebug.mode=off vendor/bin/psalm --no-cache --set-baseline=psalm-baseline.xml
.PHONY: set-baseline

cs: ## Verify coding standards
	php -d xdebug.mode=off vendor/bin/phpcs
.PHONY: cs

csfix: ## Auto-fix coding standard rules, where possible
	php -d xdebug.mode=off vendor/bin/phpcbf
.PHONY: csfix
