.PHONY: help deploy sync install dev debug dump dumpimport dbupgrade seed format typescript twitch provision
.DEFAULT_GOAL := help

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

deploy: ## Déploie une nouvelle version du site
	ssh -A $(server) 'cd $(domain) && git pull origin master && make install'

sync: ## Récupère les données depuis le serveur
	rsync -avz --ignore-existing --progress --exclude=avatars grafikart:/home/grafikart/grafikart.fr/public/uploads/ ./public/uploads/

install: vendor/autoload.php public/assets/.vite/manifest.json ## Installe les différentes dépendances
	APP_ENV=prod APP_DEBUG=0 php composer install --no-dev --optimize-autoloader
	make migrate
	APP_ENV=prod APP_DEBUG=0 php artisan optimize

dev: node_modules/time ## Lance le serveur de développement
	parallel --ungroup ::: "frankenphp run" "bun run dev"

debug:
	php -dxdebug.mode=debug -dxdebug.client_port=9003 -dxdebug.client_host=127.0.0.1 artisan serve

dump: var/dump ## Génère un dump SQL
	docker compose exec db sh -c 'PGPASSWORD="grafikart" pg_dump grafikart -U grafikart > /var/www/var/dump/dump.sql'

dumpimport: ## Import un dump SQL
	docker compose exec db sh -c 'pg_restore -c -d grafikart -U grafikart /var/dump'

dbupgrade: ## Migrate the database from the old site to the new
	php artisan migrate
	php artisan db:seed --class=DatabaseImporterSeeder

seed: vendor/autoload.php ## Génère des données dans la base de données (docker compose up doit être lancé)
	php artisan migrate:fresh --seed

format: ## Analyse le code
	bun run check
	bun run format
	./vendor/bin/pint

typescript: ## Génère les types TypeScript
	php artisan typescript:transform
	sed -i 's/ | Array<any>//g' resources/js/types/generated.d.ts

twitch:
	twitch event trigger stream.online -F http://localhost:8000/twitch/webhook -s testsecret

# -----------------------------------
# Déploiement
# -----------------------------------
provision: ## Configure la machine distante
	ansible-playbook --vault-password-file ../.vault_pass -i tools/ansible/hosts.yml tools/ansible/install.yml

# -----------------------------------
# Dépendances
# -----------------------------------
vendor/autoload.php: composer.lock
	php composer install
	touch vendor/autoload.php

node_modules/time: bun.lock
	bun bun install
	touch node_modules/time

bun.lock:
	bun install

public/assets: node_modules/time
	bun run build

var/dump:
	mkdir var/dump

public/assets/.vite/manifest.json: package.json
	bun bun install
	bun bun run build
