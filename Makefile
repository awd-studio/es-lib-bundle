#!make
include .env
export

# Variables
DOCKER = docker
DOCKER_COMPOSE = docker compose
EXEC = $(DOCKER) exec -it $(DOCKER_SERVICE_NAME_PHP)
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

.PHONY: cache-clear
cache-clear: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear

.PHONY: php
php: ## Returns a bash of the PHP container
	$(DOCKER_COMPOSE) up -d php-fpm
	$(MAKE) php-bash

.PHONY: php-bash
php-bash:
	$(DOCKER_COMPOSE) exec --user www-data php-fpm bash -l

## —— ✅ Test ——————————————————————————————————————————————————————————————————
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

## —— 🐳 Docker ———————————————————————————————————————————————————————————————
.PHONY: build
build: ## Build app with fresh images
	$(DOCKER_COMPOSE) build

.PHONY: start
start: ## Start the app
	$(MAKE) docker-start
	@$(call GREEN,"The application installed successfully.")

.PHONY: docker-start
docker-start:
	$(DOCKER_COMPOSE) up -d

.PHONY: terminate
terminate: ## Unsets all the set
	$(MAKE) stop
	$(DOCKER_COMPOSE) down --remove-orphans --volumes
	$(DOCKER_COMPOSE) rm -vsf
	@$(call GREEN,"The application was terminated successfully.")

.PHONY: rebuild
rebuild: ## Rebuilds all docker containers
	$(MAKE) terminate
	$(MAKE) init

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
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
