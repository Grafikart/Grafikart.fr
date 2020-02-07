user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
dr := $(dc) run --rm
drtest := $(dc) -f docker-compose.test.yml run --rm

.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: node_modules/time vendor/autoload.php ## Installe les différentes dépendances
	yarn run build

.PHONY: build-docker
build-docker:
	USER_ID=$(user) GROUP_ID=$(group) docker-compose build php

.PHONY: lint
lint: vendor/autoload.php ## Analyse le code
	docker run -v $(PWD):/app --rm phpstan/phpstan analyse

.PHONY: seed
seed: vendor/autoload.php ## Génère des données
	$(dr) php bin/console hautelook:fixtures:load -q
	docker-compose down

.PHONY: migrate
migrate: vendor/autoload.php ## Migre la base de donnée
	$(dr) php php bin/console doctrine:migrations:migrate

.PHONY: test
test: vendor/autoload.php ## Execute les tests
	$(drtest) php vendor/bin/phpunit

.PHONY: tt
tt: vendor/autoload.php ## Lance le watcher phpunit
	$(drtest) php vendor/bin/phpunit-watcher watch --filter="nothing"

.PHONY: clean
clean: ## Nettoie les containers laissés en fonction
	docker-compose -f docker-compose.yml -f docker-compose.test.yml down

.PHONY: dev
dev: vendor/autoload.php ## Lance le serveur de développement
	docker-compose up

.PHONY: doc
doc: ## Génère le sommaire de la documentation
	npx doctoc ./README.md

vendor/autoload.php: composer.lock
	$(dr) --no-deps php composer install
	touch vendor/autoload.php

node_modules/time: yarn.lock
	yarn
	touch node_modules/time
