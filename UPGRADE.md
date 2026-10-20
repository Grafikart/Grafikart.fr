# Plan de mise à jour Composer

Audit du 20 octobre 2026, à partir des dépendances directes de `require` et `require-dev` et du **composer.lock présent dans le répertoire de travail**, y compris ses modifications préexistantes. Les versions de départ sont celles verrouillées, pas les bornes minimales de composer.json.

Versions stables relevées auprès de Packagist avec `composer show --direct --locked --latest --format=json` et `composer outdated --direct --locked --format=json`, sur PHP 8.5.11. Les cibles sont les dernières versions signalées pour cette plateforme, au-delà des contraintes actuelles ; leur présence ne garantit pas leur compatibilité entre elles. PHP reste en `^8.4` : c'est un prérequis de plateforme, pas un paquet à installer.

Ce document prépare la migration. Depuis l'audit initial, `shaffe/laravel-dkim` a été retiré de composer.json et composer.lock ; les versions ci-dessous restent celles relevées lors de l'audit. Les dépendances transitives seront résolues lors de la migration et ne sont pas inventoriées individuellement ici.

## Mises à jour majeures — 11 paquets

| Paquet | Usage | Version verrouillée | Cible stable | Contrainte proposée |
| --- | --- | --- | --- | --- |
| `fruitcake/laravel-debugbar` | Développement | 3.16.5 | 4.4.4 | `^4.4.4` |
| `inertiajs/inertia-laravel` | Production | 2.0.21 | 3.5.1 | `^3.5.1` |
| `laravel/boost` | Développement | 1.8.13 | 2.10.3 | `^2.10.3` |
| `laravel/framework` | Production | 12.70.0 | 13.38.0 | `^13.38.0` |
| `laravel/tinker` | Production | 2.11.1 | 3.0.2 | `^3.0.2` |
| `league/glide` | Production | 3.2.0 | 4.2.0 | `^4.2.0` |
| `mallardduck/blade-lucide-icons` | Production | 1.26.32 | 2.0.31 | `^2.0.31` |
| `pestphp/pest` | Développement | 4.7.8 | 5.3.3 | `^5.3.3` |
| `pestphp/pest-plugin-laravel` | Développement | 4.1.0 | 5.0.1 | `^5.0.1` |
| `spatie/laravel-typescript-transformer` | Production | 2.6.0 | 3.3.0 | `^3.3.0` |
| `stripe/stripe-php` | Production | 19.4.1 | 22.0.0 | `^22.0.0` |

Les contraintes proposées constituent des cibles de migration, pas un ensemble actuellement installable.

### inertiajs/inertia-laravel : 2 → 3

- Mettre à niveau ensemble `@inertiajs/react` et `@inertiajs/core` dans package.json (actuellement en 2.x). React 19 est déjà présent.
- Déplacer `testing.page_paths` et `testing.page_extensions` de `config/inertia.php` vers `pages.paths` et `pages.extensions`. Conserver le chemin réel `resources/js/pages` pour les assertions `assertInertia`.
- La directive `@inertia` change de format de données initiales : vider les vues compilées au déploiement et vérifier le démarrage du CMS et le SSR configuré.
- Le transport interne abandonne Axios ; les événements `invalid`/`exception` deviennent `httpException`/`networkError`, `router.cancel` devient `cancelAll`, et `Inertia::lazy` disparaît. Aucun usage direct de ces anciennes API repéré ; vérifier les intégrations lors de la migration du client.
- Le comportement temporel de `processing` et `progress` change : vérifier les formulaires du CMS et leur état de chargement.

Source : [guide Inertia 3](https://inertiajs.com/docs/v3/getting-started/upgrade-guide).

### stripe/stripe-php : 19 → 22, en passant par 20 et 21

- Les changements de version d'API par défaut sont cumulatifs : Clover → Dahlia en 20, puis `2026-09-30.endive` en 22. Examiner les changements Billing/Checkout selon les opérations utilisées et vérifier paiements, abonnements, taxes, remboursements et webhooks.
- **Impact concret :** `app/Infrastructure/Payment/Stripe/StripeApi.php` appelle `Stripe::setApiVersion('2026-01-28.clover')`, puis `new StripeClient($privateKey)`. Le client a son propre défaut `stripe_version=ApiVersion::CURRENT` : le réglage global ne fige pas ce client. Pour différer la migration d'API, passer explicitement `stripe_version` dans le tableau de configuration du constructeur, puis valider le comportement.
- La v20 rejette les parseurs de webhooks inadaptés au format reçu. Le projet utilise `Webhook::constructEvent` : conserver des événements snapshot et vérifier signatures et fixtures avec la version configurée côté Stripe.
- Les annotations de types évoluent en 21/22 : vérifier les erreurs Larastan éventuelles autour des réponses et erreurs Stripe.

Sources : [changelog cumulatif](https://github.com/stripe/stripe-php/blob/v22.0.0/CHANGELOG.md), [migration 20](https://github.com/stripe/stripe-php/wiki/Migration-guide-for-v20), [migration 22](https://github.com/stripe/stripe-php/wiki/Migration-guide-for-v22), [configuration du client](https://github.com/stripe/stripe-php/blob/v22.0.0/lib/BaseStripeClient.php).

## Mises à jour mineures

Aucune mise à jour mineure restante entre les versions verrouillées et les dernières versions stables relevées. Des mineures intermédiaires sont incluses dans les migrations majeures ci-dessus.

## Mises à jour patch

| Paquet | Version verrouillée | Cible stable | Contrainte actuelle |
| --- | --- | --- | --- |
| `laravel/wayfinder` | 0.1.20 | 0.1.23 | `^0.1.9`, déjà compatible |

Wayfinder est en version 0.x : le classement patch décrit le numéro de version, pas une garantie d'absence de rupture. Après mise à jour, régénérer/vérifier les routes et actions TypeScript consommées par le frontend. [Historique officiel](https://github.com/laravel/wayfinder/releases).

## Dépendances déjà à jour — 21 paquets

Ces dépendances ont également été contrôlées ; aucune nouvelle version stable n'a été signalée par Composer sur la plateforme de l'audit.

| Paquet | Version verrouillée = dernière version relevée |
| --- | --- |
| `erusev/parsedown` | 1.8.0 |
| `fakerphp/faker` | 1.24.1 |
| `google/apiclient` | 2.20.1 |
| `larastan/larastan` | 3.13.0 |
| `laravel-lang/common` | 6.8.0 |
| `laravel/fortify` | 1.41.0 |
| `laravel/pail` | 1.2.7 |
| `laravel/pint` | 1.32.1 |
| `laravel/sail` | 1.69.0 |
| `laravel/sanctum` | 4.3.3 |
| `laravel/socialite` | 5.31.0 |
| `league/csv` | 9.28.0 |
| `mockery/mockery` | 1.6.15 |
| `mvanduijker/laravel-mercure-broadcaster` | 3.9.0 |
| `nunomaduro/collision` | 8.9.5 |
| `paypal/paypal-server-sdk` | 2.4.0 |
| `spatie/enum` | 3.13.0 |
| `spatie/flysystem-dropbox` | 3.1.0 |
| `spatie/laravel-data` | 4.23.0 |
| `spatie/laravel-responsecache` | 8.6.0 |
| `symfony/intl` | 8.1.5 |

`spatie/enum` utilise actuellement `*` : envisager une contrainte explicite `^3.13` pour éviter d'accepter une future majeure sans revue. Cela ne constitue pas une mise à jour de version.

## Ordre de migration proposé

1. Mettre à jour Wayfinder, puis les outils de développement par lots cohérents ; garder Pest et son plugin ensemble.
2. Préparer ensemble Laravel 13, Tinker 3 et Boost 2 ; relancer le solveur après le retrait de `shaffe/laravel-dkim` pour détecter d'éventuels conflits supplémentaires.
3. Migrer Inertia côté PHP et JavaScript dans le même lot, puis TypeScript Transformer et Glide séparément.
4. Traiter Stripe avec une décision explicite sur la version d'API et une validation des parcours de paiement en environnement de test.
5. Après modification des contraintes, lancer d'abord `composer update -W --dry-run --no-scripts`, puis effectuer la mise à jour et examiner les modifications du lock et les scripts Composer. Ne pas utiliser `--ignore-platform-reqs` pour masquer un conflit.
6. Exécuter les tests existants (`composer test`), l'analyse statique utilisée par le projet, `bun run check` et `bun run build`. Vérifier aussi DKIM, images, sessions/CSRF, CMS et webhooks selon les lots appliqués.

Glide, TypeScript Transformer et le lot Laravel 13 / Tinker 3 / Boost 2 / Pest 5 / Debugbar 4 sont installés. Pour ce dernier lot, les 422 tests, `bun run check`, `bun run build`, la génération TypeScript et les scripts Composer passent. PHPStan signale les cinq erreurs préexistantes liées aux modèles de révisions. Le contrôle Pint global conserve ses erreurs de formatage préexistantes. Les autres versions cibles restent à migrer et à valider.
