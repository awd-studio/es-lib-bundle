# Variables
DOCKER = docker
DOCKER_COMPOSE = docker compose
EXEC = $(DOCKER) exec -it awdes-bundle-php-fpm
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer

# Colors
GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

## —— 🔥 App ——————————————————————————————————————————————————————————————————
.PHONY: init
init: ## Init the project
	$(MAKE) build
	$(MAKE) start
	$(COMPOSER) install --prefer-dist
	$(COMPOSER) dev-tools-setup
	@$(call GREEN,"The application installed successfully.")

.PHONY: cache-clear
cache-clear: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear

.PHONY: php
php: ## Returns a bash of the PHP container
	$(DOCKER_COMPOSE) up -d awdes-bundle-php-fpm
	$(MAKE) php-bash

.PHONY: php-bash
php-bash:
	$(DOCKER_COMPOSE) exec awdes-bundle-php-fpm bash -l

## —— ✅ Test ——————————————————————————————————————————————————————————————————
.PHONY: tests
tests: ## Run all tests
	$(PHP) bin/phpunit --testdox tests/unit/

.PHONY: unit-test
unit-test: ## Run unit tests
	$(PHP) bin/phpunit --testdox tests/unit/

## —— 🐳 Docker ———————————————————————————————————————————————————————————————
.PHONY: build
build: ## Build app with fresh images
	$(DOCKER_COMPOSE) build

.PHONY: start
start: ## Start the app
	$(MAKE) docker-start
	@$(call GREEN,"The application is available at: $(HOST).")

.PHONY: docker-start
docker-start:
	$(DOCKER_COMPOSE) up -d

.PHONY: rebuild
rebuild: ## Rebuilds all docker containers
	$(MAKE) stop
	$(DOCKER_COMPOSE) up -d --no-deps --build

.PHONY: stop
stop: ## Stop app
	$(MAKE) docker-stop

.PHONY: docker-stop
docker-stop:
	$(DOCKER_COMPOSE) stop
	@$(call GREEN,"The containers are now stopped.")

## —— 🎻 Composer —————————————————————————————————————————————————————————————
.PHONY: composer-install
composer-install: ## Install dependencies
	$(COMPOSER) install

.PHONY: composer-update
composer-update: ## Update dependencies
	$(COMPOSER) update

.PHONY: composer-clear-cache
composer-clear-cache: ## clear-cache dependencies
	$(COMPOSER) clear-cache

## —— 🛠️ Others ——————————————————————————————————————————————————————————————
.PHONY: help
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
