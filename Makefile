user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
dr := $(dc) run --rm
de := docker-compose exec
sy := $(de) php bin/console
drtest := $(dc) -f docker-compose.test.yml run --rm

.DEFAULT_GOAL := help
.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: public/assets vendor/autoload.php ## Installe les différentes dépendances

.PHONY: build-docker
build-docker:
	# $(dc) pull --ignore-pull-failures
	# $(dc) build --force-rm
	$(dc) build php

.PHONY: dev
dev: vendor/autoload.php node_modules/time ## Lance le serveur de développement
	$(dc) up

.PHONY: clean
clean: ## Nettoie les containers
	$(dc) -f docker-compose.yml -f docker-compose.test.yml -f docker-compose.import.yml down --volumes

.PHONY: seed
seed: vendor/autoload.php ## Génère des données dans la base de données (docker-compose up doit être lancé)
	$(sy) doctrine:migrations:migrate -q
	$(sy) doctrine:schema:validate -q
	$(sy) hautelook:fixtures:load -q

.PHONY: migration
migration: vendor/autoload.php ## Génère les migrations
	$(sy) make:migration

.PHONY: migrate
migrate: vendor/autoload.php ## Migre la base de données (docker-compose up doit être lancé)
	$(sy) doctrine:migrations:migrate -q

.PHONY: rollback
rollback:
	$(sy) doctrine:migration:migrate prev

.PHONY: import
import: vendor/autoload.php ## Import les données du site actuel
	$(dc) -f docker-compose.import.yml up -d
	$(sy) doctrine:migrations:migrate -q
	$(sy) app:import reset
	$(sy) app:import users
	$(sy) app:import tutoriels
	$(sy) app:import formations
	$(sy) app:import blog
	$(sy) app:import comments
	$(sy) app:import forum
	$(dc) -f docker-compose.import.yml stop

.PHONY: test
test: vendor/autoload.php ## Execute les tests
	$(drtest) phptest bin/console doctrine:schema:validate --skip-sync
	$(drtest) phptest vendor/bin/phpunit
	$(dr) --no-deps node yarn run test

.PHONY: tt
tt: vendor/autoload.php ## Lance le watcher phpunit
	$(drtest) phptest vendor/bin/phpunit-watcher watch --filter="nothing"

.PHONY: lint
lint: vendor/autoload.php ## Analyse le code
	docker run -v $(PWD):/app --rm phpstan/phpstan analyse

.PHONY: doc
doc: ## Génère le sommaire de la documentation
	npx doctoc ./README.md

vendor/autoload.php: composer.lock
	$(dr) --no-deps php composer install
	touch vendor/autoload.php

node_modules/time: yarn.lock
	$(dr) --no-deps node yarn
	touch node_modules/time

public/assets: node_modules/time
	$(dr) --no-deps node yarn run build

