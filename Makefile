user := $(shell id -u)
group := $(shell id -g)
docker := `command -v docker`
php := php
composer := composer
symfony := symfony

ifdef docker
dr := USER_ID=$(user) GROUP_ID=$(group) docker-compose run --rm --service-ports
php := $(dr) php php
composer := $(dr) php composer
endif

.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: dev
dev: ## Lance le serveur de développement
	make -j 2 php-server

build: ## Construit les images


.PHONY: doc
doc: ## Génère le sommaire de la documentation
	npx doctoc ./README.md

.PHONY: php-server
php-server: vendor/autoload.php ## Démarre le serveur interne de PHP
	$(php) -S 0.0.0.0:8000 -t public

vendor/autoload.php: composer.lock
	$(composer) install
