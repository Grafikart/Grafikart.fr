user := $(shell id -u)
group := $(shell id -g)
docker := `command -v docker`
dr := USER_ID=$(user) GROUP_ID=$(group) docker-compose run --rm --service-ports
php := $(dr) php php
symfony := $(dr) php symfony
composer := $(dr) php composer

.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: lint
lint: vendor/autoload.php ## Analyse le code
	docker run -v $(PWD):/app --rm phpstan/phpstan analyse

.PHONY: test
test: vendor/autoload.php
	$(dr) php bin/phpunit

.PHONY: dev
dev: vendor/autoload.php ## Lance le serveur de développement
	docker-compose up

.PHONY: build
build: ## Construit les images
	USER_ID=$(user) GROUP_ID=$(group) docker-compose build php

.PHONY: doc
doc: ## Génère le sommaire de la documentation
	npx doctoc ./README.md

vendor/autoload.php: composer.lock
	$(composer) install
