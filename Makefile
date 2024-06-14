isDocker := $(shell docker info > /dev/null 2>&1 && echo 1)
isProd := $(shell grep "APP_ENV=prod" .env.local > /dev/null && echo 1)
domain := "grafikart.fr"
server := "grafikart"
user := $(shell id -u)
group := $(shell id -g)

sy := php bin/console
bun :=
php :=
ifeq ($(isDocker), 1)
	ifneq ($(isProd), 1)
		dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
		dcimport := USER_ID=$(user) GROUP_ID=$(group) docker-compose -f docker-compose.import.yml
		de := docker-compose exec
		dr := $(dc) run --rm
		drtest := $(dc) -f docker-compose.test.yml run --rm
		sy := $(de) php bin/console
		bun := $(dr) bun
		php := $(dr) --no-deps php
	endif
endif

.DEFAULT_GOAL := help
.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: deploy
deploy: ## Déploie une nouvelle version du site
	ssh -A $(server) 'cd $(domain) && git pull origin master && make install'

.PHONY: sync
sync: ## Récupère les données depuis le serveur
	rsync -avz --ignore-existing --progress --exclude=avatars grafikart:/home/grafikart/grafikart.fr/public/uploads/ ./public/uploads/

.PHONY: install
install: vendor/autoload.php public/assets/manifest.json ## Installe les différentes dépendances
	APP_ENV=prod APP_DEBUG=0 $(php) composer install --no-dev --optimize-autoloader
	make migrate
	APP_ENV=prod APP_DEBUG=0 $(sy) cache:clear
	APP_ENV=prod APP_DEBUG=0 $(sy) cache:pool:clear cache.global_clearer
	$(sy) messenger:stop-workers
	sudo service php8.2-fpm reload

.PHONY: build-docker
build-docker:
	$(dc) pull --ignore-pull-failures
	$(dc) build php
	$(dc) build messenger

.PHONY: dev
dev: vendor/autoload.php node_modules/time ## Lance le serveur de développement
	$(dc) up

.PHONY: devmac
devmac: ## Sur MacOS on ne préfèrera exécuter PHP en local pour les performances
	docker-compose -f docker-compose.macos.yml up

.PHONY: dump
dump: var/dump ## Génère un dump SQL
	$(de) db sh -c 'PGPASSWORD="grafikart" pg_dump grafikart -U grafikart > /var/www/var/dump/dump.sql'

.PHONY: dumpimport
dumpimport: ## Import un dump SQL
	$(de) db sh -c 'pg_restore -c -d grafikart -U grafikart /var/www/var/dump'

.PHONY: seed
seed: vendor/autoload.php ## Génère des données dans la base de données (docker-compose up doit être lancé)
	$(sy) doctrine:migrations:migrate -q
	$(sy) app:seed -q

.PHONY: migration
migration: vendor/autoload.php ## Génère les migrations
	$(sy) make:migration

.PHONY: migrate
migrate: vendor/autoload.php ## Migre la base de données (docker-compose up doit être lancé)
	$(sy) doctrine:migrations:migrate -q

.PHONY: rollback
rollback:
	$(sy) doctrine:migration:migrate prev

.PHONY: test
test: vendor/autoload.php node_modules/time ## Execute les tests
	$(drtest) phptest bin/console doctrine:schema:validate --skip-sync
	$(drtest) phptest vendor/bin/paratest -p 4 --runner=WrapperRunner
	# $(drtest) phptest vendor/bin/phpunit --filter=ContentSubscriber
	$(bun) bun run test

.PHONY: tt
tt: vendor/autoload.php ## Lance le watcher phpunit
	$(drtest) phptest bin/console cache:clear --env=test
	$(drtest) phptest vendor/bin/phpunit-watcher watch --filter="nothing"

.PHONY: lint
lint: vendor/autoload.php ## Analyse le code
	docker run -v $(PWD):/app -w /app -t --rm php:8.2-cli-alpine php -d memory_limit=-1 bin/console lint:container
	docker run -v $(PWD):/app -w /app -t --rm php:8.2-cli-alpine php -d memory_limit=-1 ./vendor/bin/phpstan analyse

.PHONY: security-check
security-check: vendor/autoload.php ## Check pour les vulnérabilités des dependencies
	$(de) php local-php-security-checker --path=/var/www

.PHONY: format
format: ## Formate le code
	npx prettier-standard --lint --changed "assets/**/*.{js,css,jsx}"
	docker run -v $(PWD):/app -w /app -t --rm php:8.2-cli-alpine php -d memory_limit=-1 ./vendor/bin/phpcbf
	docker run -v $(PWD):/app -w /app -t --rm php:8.2-cli-alpine php -d memory_limit=-1 ./vendor/bin/php-cs-fixer fix

.PHONY: refactor
refactor: ## Reformate le code avec rector
	docker run -v $(PWD):/app -w /app -t --rm php:8.2-cli-alpine php -d memory_limit=-1 ./vendor/bin/rector process --clear-cache

.PHONY: doc
doc: ## Génère le sommaire de la documentation
	npx doctoc ./README.md

# -----------------------------------
# Déploiement
# -----------------------------------
.PHONY: provision
provision: ## Configure la machine distante
	ansible-playbook --vault-password-file .vault_pass -i tools/ansible/hosts.yml tools/ansible/install.yml

# -----------------------------------
# Dépendances
# -----------------------------------
vendor/autoload.php: composer.lock
	$(php) composer install
	touch vendor/autoload.php

node_modules/time: bun.lockb
	$(bun) bun install
	touch node_modules/time

bun.lockb:
	$(bun) bun install

public/assets: node_modules/time
	$(bun) run build

var/dump:
	mkdir var/dump

public/assets/manifest.json: package.json
	$(bun) bun install
	$(bun) bun run build
