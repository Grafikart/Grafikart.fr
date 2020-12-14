isDocker := $(shell docker info > /dev/null 2>&1 && echo 1)
server := "ubuntu@beta.grafikart.fr"
user := $(shell id -u)
group := $(shell id -g)
ifeq ($(isDocker), 1)
	dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose -f docker-compose.yml -f docker-compose.dev.yml
	dcimport := USER_ID=$(user) GROUP_ID=$(group) docker-compose -f docker-compose.import.yml
	dcprod := docker-compose -f docker-compose.yml -f docker-compose.prod.yml
	de := docker-compose exec
	dr := $(dc) run --rm
	sy := $(de) php bin/console
	drtest := $(dc) -f docker-compose.test.yml run --rm
	node := $(dr) node
	php := $(dr) --no-deps php
else
	sy := php bin/console
	node :=
	php :=
endif

.DEFAULT_GOAL := help
.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: deploy
deploy:
	$(node) yarn run build
	rsync -avz --progress --delete ./public/assets/ $(server):~/beta.grafikart.fr/public/assets/
	ssh -A $(server) 'cd beta.grafikart.fr && git pull origin master && make install'

.PHONY: install
install: vendor/autoload.php ## Installe les différentes dépendances
	$(php) composer install --no-dev --optimize-autoloader
	make migrate
	APP_ENV=prod APP_DEBUG=0 $(sy) cache:clear
	$(sy) cache:pool:clear cache.global_clearer
	$(sy) messenger:stop-workers

.PHONY: build-docker
build-docker:
	$(dc) pull --ignore-pull-failures
	$(dc) build php
	$(dc) build messenger
	$(dc) build node

.PHONY: dev
dev: vendor/autoload.php node_modules/time ## Lance le serveur de développement
	$(dc) up

.PHONY: preprod
preprod: vendor/autoload.php public/assets ## Lance le serveur de preprod
	$(dcprod) up

.PHONY: dump
dump: var/dump ## Génère un dump SQL
	$(de) db sh -c 'PGPASSWORD="grafikart" pg_dump grafikart -U grafikart > /var/www/var/dump/dump.sql'

.PHONY: dumpimport
dumpimport: var/dump ## Import un dump SQL
	$(de) db sh -c 'psql grafikart < /var/www/var/dump/dump.sql'

.PHONY: seed
seed: vendor/autoload.php ## Génère des données dans la base de données (docker-compose up doit être lancé)
	$(sy) doctrine:migrations:migrate -q
	$(sy) doctrine:schema:validate -q
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
test: vendor/autoload.php ## Execute les tests
	$(drtest) phptest bin/console doctrine:schema:validate --skip-sync
	$(drtest) phptest vendor/bin/phpunit
	$(node) yarn run test

.PHONY: tt
tt: vendor/autoload.php ## Lance le watcher phpunit
	$(drtest) phptest bin/console cache:clear --env=test
	$(drtest) phptest vendor/bin/phpunit-watcher watch --filter="nothing"

.PHONY: lint
lint: vendor/autoload.php ## Analyse le code
	docker run -v $(PWD):/app -w /app --rm php:7.4-cli-alpine php -d memory_limit=-1 ./vendor/bin/phpstan analyse

.PHONY: format
format: ## Formate le code
	npx prettier-standard --lint --changed "assets/**/*.{js,css,jsx}"
	./vendor/bin/phpcbf
	./vendor/bin/php-cs-fixer fix

.PHONY: doc
doc: ## Génère le sommaire de la documentation
	npx doctoc ./README.md

# -----------------------------------
# Déploiement
# -----------------------------------
.PHONY: provision
provision: ## Configure la machine distante
	ansible-playbook -i tools/ansible/hosts.yml tools/ansible/install.yml

.PHONY: import
import: vendor/autoload.php ## Importe les données du site actuel et génère un dump en sortie
	gunzip -k downloads/grafikart.gz
	$(dcimport) stop
	$(dcimport) up -d
	rsync -avz --ignore-existing --progress --exclude=avatars --exclude=tmp --exclude=users grafikart:/home/www/grafikart.fr/shared/public/uploads/ ./public/old/
	tar -xf downloads/grafikart.tar.gz -C downloads/
	$(sy) doctrine:migrations:migrate -q
	$(sy) app:import reset
	$(sy) app:import users
	$(sy) app:import tutoriels
	$(sy) app:import formations
	$(sy) app:import blog
	$(sy) app:import comments
	$(sy) app:import forum
	$(sy) app:import badges
	$(sy) app:import transactions
	$(dcimport) stop
	$(dc) up -d
	sleep 5
	$(de) db sh -c 'PGPASSWORD="grafikart" pg_dump -U grafikart -Ft grafikart --clean > /var/www/var/dump.tar'
	ansible-playbook -i tools/ansible/hosts.yml tools/ansible/import.yml
	rm -rf var/dump.tar
	rsync -avz --ignore-existing --progress ./public/uploads/ $(server):~/beta.grafikart.fr/public/uploads/

# -----------------------------------
# Dépendances
# -----------------------------------
vendor/autoload.php: composer.lock
	$(php) composer install
	touch vendor/autoload.php

node_modules/time: yarn.lock
	$(node) yarn
	touch node_modules/time

public/assets: node_modules/time
	$(node) yarn run build

var/dump:
	mkdir var/dump
