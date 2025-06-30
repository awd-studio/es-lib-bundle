#!make

.DEFAULT_GOAL := help

# ———————————————————————— 🔧 Environment Imports ———————————————————————————————
-include .env.dist
-include .env
-include .env.dev
-include .env.override
export

# ———————————————————————— 🧩 Variables —————————————————————————————————————————
MERGED_FILE := .env
ENV_SOURCES := $(wildcard .env.dist .env .env.$(APP_ENV) .env.override)

# Docker command helpers
DOCKER = docker
MAKE_SILENT = @$(MAKE) --no-print-directory

# Docker Compose with auto env-merge
DOCKER_COMPOSE = $(MAKE_SILENT) env-merge >/dev/null && docker compose --env-file $(MERGED_FILE)

# Log formatting helpers
GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

# PHP container interaction
EXEC = $(DOCKER) exec -it $(DOCKER_SERVICE_NAME_PHP)
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer

## ————————————————— 🔥 Project Lifecycle ———————————————————————————————————————
.PHONY: init
init: ## Init the project
	$(DOCKER_COMPOSE) build
	$(MAKE_SILENT) start
	$(COMPOSER) install --prefer-dist
	$(COMPOSER) dev-tools-setup
	@$(call GREEN,"The application installed successfully.")

.PHONY: rebuild
rebuild: ## Rebuild all Docker containers
	$(MAKE_SILENT) terminate
	$(MAKE_SILENT) init

.PHONY: terminate
terminate: ## Unsets all the set
	$(MAKE_SILENT) stop
	$(DOCKER_COMPOSE) down --remove-orphans --volumes
	$(DOCKER_COMPOSE) rm -vsf
	@$(call GREEN,"The application was terminated successfully.")

## ————————————————— 🏁 Runtime Control —————————————————————————————————————————
.PHONY: start
start: ## Start the application
	$(DOCKER_COMPOSE) up -d
	@$(call GREEN,"The application installed successfully.")

.PHONY: stop
stop: ## Stop app
	$(DOCKER_COMPOSE) stop
	@$(call GREEN,"The containers are now stopped.")

.PHONY: down
down: ## Completely remove all containers
	$(DOCKER_COMPOSE) down
	@$(call GREEN,"The containers are now down.")

.PHONY: php
php: ## Open Bash shell inside PHP container
	$(DOCKER_COMPOSE) up -d php-fpm
	$(DOCKER_COMPOSE) exec php-fpm bash -l

## ————————————————— ✅️ Quality & Testing ———————————————————————————————————————
.PHONY: tests
tests: ## Run all tests
	$(DOCKER_COMPOSE) up -d php-fpm
	$(COMPOSER) test
	$(DOCKER_COMPOSE) stop

.PHONY: phpunit
phpunit: ## Runs phpunit
	$(DOCKER_COMPOSE) up -d php-fpm
	$(COMPOSER) phpunit
	$(DOCKER_COMPOSE) stop

.PHONY: unit-tests
unit-tests: ## Run unit tests
	$(DOCKER_COMPOSE) up -d php-fpm
	$(PHP) vendor/bin/phpunit --testdox tests/Unit/
	$(DOCKER_COMPOSE) stop

.PHONY: code-fix
code-fix: ## Runs quality tools to fix common issues
	$(DOCKER_COMPOSE) up -d
	$(COMPOSER) code-fix

.PHONY: cache-clear
cache-clear: ## Clear Symfony cache
	$(SYMFONY_CONSOLE) cache:clear

## ————————————————— 🎻 Composer —————————————————————————————————————————————
.PHONY: composer-install
composer-install: ## Install composer dependencies
	$(COMPOSER) install

.PHONY: composer-update
composer-update: ## Update composer dependencies
	$(COMPOSER) update

.PHONY: composer-clear-cache
composer-clear-cache: ## Clear composer cache
	$(COMPOSER) clear-cache

.PHONY: composer-normalize
composer-normalize: ## Normalize composer dependencies
	$(COMPOSER) normalize

## ————————————————— 🛠️ Utilities ——————————————————————————————————————————————
.PHONY: env-merge
env-merge: ## Generate $(MERGED_FILE) from all env layers
	@NEW_ENV=$$(cat /dev/null \
		$(shell [ -f .env.dist ] && echo .env.dist) \
		$(shell [ -f .env ] && echo .env) \
		$(shell [ -f .env.dev ] && echo .env.dev) \
		$(shell [ -f .env.override ] && echo .env.override) \
		| grep -v '^#' | grep -v '^\s*$$' | awk -F= '!seen[$$1]++'); \
	OLD_ENV=$$(cat $(MERGED_FILE) 2>/dev/null || echo ""); \
	if [ "$$NEW_ENV" != "$$OLD_ENV" ]; then \
		echo "$$NEW_ENV" > $(MERGED_FILE); \
		echo "🔄 Regenerated $(MERGED_FILE)"; \
	else \
		echo "✅ $(MERGED_FILE) is up to date."; \
	fi

## ————————————————— 📚 Help ————————————————————————————————————————————————————
.PHONY: help
help: ## Show all commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
