.PHONY: help
.DEFAULT_GOAL = help

dc = docker-compose
de = $(dc) exec
composer = $(de) php memory_limit=1 /usr/local/bin/composer

## —— Docker 🐳  ———————————————————————————————————————————————————————————————
.PHONY: install
install:	## Installation projet
	cp .env.exemple .env
	$(dc) up -d
	$(de) php bash -c 'composer install'
	$(de) php bash -c 'bin/console app:post-install'
	$(de) php bash -c 'bin/console app:regenerate-app-secret'
	$(de) php bash -c 'bin/console doctrine:migrations:migrate --no-interaction'
	$(de) php bash -c 'bin/console app:make-user'

.PHONY: start
start:	## start container
	$(dc) up -d

.PHONY: build
start:	## build container
	$(dc) up --build -d

.PHONY: in-dc
in-dc:	## connexion container php
	$(de) php bash

.PHONY: delete
delete:	## delete container
	$(dc) down
	$(dc) kill
	$(dc) rm

## —— Quality Assurance 🛠️️ ———————————————————————————————————————————————————————————————
.PHONY: phpstan
phpstan:  ## phpstan
	vendor/bin/phpstan analyse --memory-limit=2G

.PHONY: phpcs
phpcs: ## PHP_CodeSnifer Geolid flavoured (https://github.com/Geolid/phpcs)
	vendor/bin/phpcs
	vendor/bin/php-cs-fixer fix --dry-run --diff

.PHONY: phpcs-fix
phpcs-fix: ## Automatically correct coding standard violations
	vendor/bin/phpcbf
	vendor/bin/php-cs-fixer fix

.PHONY: twigcs
twigcs: ## Twigcs (https://github.com/allocine/twigcs)
	vendor/bin/twigcs templates

## —— Others 🛠️️ ———————————————————————————————————————————————————————————————
help: ## listing command
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'