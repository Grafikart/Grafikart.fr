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

### Dépendance DKIM retirée

`shaffe/laravel-dkim` a été retiré de composer.json et composer.lock. Il bloquait la simulation initiale de Laravel 13, car sa version 1.0.0 exige `illuminate/mail ^10|^11|^12`. Ce conflit de contraintes est donc levé.

Le fournisseur du paquet était enregistré automatiquement par Laravel, mais sa signature ne s'activait que si `dkim.enabled` était vrai. Cette option est désactivée dans la configuration locale vérifiée ; l'utilisation effective du paquet en production n'est pas confirmée. Son enregistrement automatique ne prouve pas qu'il signait les courriels.

Le projet conserve sa propre implémentation dans `app/Infrastructure/Mailer/Mailer.php`, `MailManager.php` et `MailerServiceProvider.php`. Aucun nouvel écouteur DKIM n'est présent dans l'état actuel du projet.

La simulation initiale s'arrêtait sur ce conflit : la compatibilité globale des versions cibles reste à vérifier avec une nouvelle résolution après retrait du paquet. Source : [manifest du package retiré](https://github.com/shaffe-fr/laravel-dkim/blob/v1.0.0/composer.json).

### laravel/framework : 12 → 13

- PHP 8.4 minimum : déjà couvert par composer.json. Migrer également Tinker vers 3 et Boost vers 2.
- Le middleware CSRF évolue vers `PreventRequestForgery`, avec prise en compte de `Sec-Fetch-Site`. `bootstrap/app.php` exclut actuellement `*` dans `validateCsrfTokens` : vérifier les POST, connexions et webhooks après migration ; l'ancien middleware devient un alias déprécié.
- Si les nouvelles configurations du squelette sont adoptées : `cache.serializable_classes=false` affecte les objets en cache. `app/Http/Front/PathController.php` met un `PathViewData` en cache : autoriser les classes nécessaires ou stocker des tableaux.
- Toujours en cas d'adoption du nouveau squelette : `session.serialization=json` invalide les sessions existantes sérialisées en PHP. Prévoir les reconnexions. Ces deux options ne doivent pas être présentées comme automatiquement appliquées par Composer à la configuration existante.
- `upsert` exige une clé `uniqueBy` non vide ; l'appel de `SettingsService.php` fournit déjà `['key']`.

Source : [guide Laravel 13](https://laravel.com/framework/docs/13.x/upgrade).

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

### league/glide : 3 → 4

Migration effectuée : `league/glide` 4.2.0 (`^4.2.0`), `intervention/image` 4.4.0 et `intervention/gif` 5.0.1 sont verrouillés. Le contrôleur et la fabrique de réponses restent compatibles sans modification. Les tests de `tests/Unit/Infrastructure/ImageControllerTest.php` valident avec GD le recadrage, l'encodage WebP, le contenu en cache, les en-têtes HTTP, la réponse conditionnelle 304 et le rejet des signatures invalides. Imagick n'est pas installé sur la plateforme de validation.

- Glide passe à Intervention Image 4 : vérifier le pilote GD/Imagick configuré et le rendu des recadrages et encodages WebP de `app/Http/Front/ImageController.php`.
- Un format invalide déclenche désormais une exception. Le contrôleur impose actuellement `fm=webp`, ce qui limite ce risque ; il ne capture que `SignatureException`.
- Le traitement de l'alpha des filigranes change avec Intervention 4. Aucun filigrane n'est configuré dans ce contrôleur ; impact conditionnel si d'autres transformations sont ajoutées.
- Vérifier aussi la fabrique personnalisée `app/Infrastructure/Image/LaravelResponseFactory.php`, les en-têtes HTTP et le cache d'images après résolution des dépendances.

Source : [notes officielles Glide 4](https://github.com/thephpleague/glide/releases#4.0.0).

### spatie/laravel-typescript-transformer : 2 → 3

- La configuration v3 passe par un `TypeScriptTransformerServiceProvider` et `TypeScriptTransformerConfigFactory`. Reprendre explicitement la configuration v2 de `config/typescript-transformer.php` : découverte dans app/, remplacements de dates, enums et format de sortie.
- Installer/enregistrer le fournisseur via `php artisan typescript:install`, puis configurer l'extension `LaravelDataTypeScriptTransformerExtension` pour les nombreux DTO Spatie Data du projet.
- Préserver le contrat de `resources/js/types/generated.d.ts` : le choix de writer et la localisation des types doivent être revus avant de régénérer. Comparer les types exportés, imports, nullabilité et propriétés facultatives, puis exécuter `bun run check` et `bun run build`.

Sources : [installation Laravel v3](https://spatie.be/docs/typescript-transformer/v3/laravel/installation-and-setup), [intégration Laravel Data](https://spatie.be/docs/typescript-transformer/v3/laravel/laravel-data), [writers et première génération](https://spatie.be/docs/typescript-transformer/v3/getting-started/first-run).

### pestphp/pest : 4 → 5 et pestphp/pest-plugin-laravel : 4 → 5

- Migrer les deux paquets ensemble : le plugin doit suivre la majeure de Pest.
- PHP 8.4 minimum, déjà couvert ; Pest 5 passe de PHPUnit 12 à PHPUnit 13.
- PHPUnit 13 retire notamment `assertContainsOnly`, `assertNotContainsOnly`, `containsOnly`, `Assert::isType`, `RunClassInSeparateProcess` et `CoversNothing` sur les méthodes ; `any()` devient déprécié avec avertissement. Aucun usage de ces assertions/attributs ni `->any()` repéré dans tests/.
- Exécuter la suite existante et vérifier phpunit.xml et les éventuelles extensions après installation ; la compatibilité du solveur seul ne valide pas les tests.

Sources : [guide Pest 5](https://pestphp.com/docs/upgrade-guide), [changements PHPUnit 13](https://github.com/sebastianbergmann/phpunit/blob/13.0.0/ChangeLog-13.0.md).

### fruitcake/laravel-debugbar : 3 → 4

- Le namespace `Barryvdh\Debugbar` devient `Fruitcake\LaravelDebugbar`. Les helpers `start_measure`, `add_measure`, `stop_measure`, `measure` et certains collecteurs/stockages sont supprimés ; aucun appel direct repéré dans l'application.
- Comparer le fichier publié `config/debugbar.php` aux nouveaux réglages : collecteur SQL (`bindings` → `params`, `hiddens` → `masked`) et configuration Inertia dédiée.
- Vérifier la barre et les requêtes du CMS en développement. php-debugbar 3 retire jQuery et Font Awesome, à prendre en compte pour d'éventuelles personnalisations.

Source : [guide Debugbar 4](https://github.com/fruitcake/laravel-debugbar/blob/master/UPGRADE.md).

### laravel/boost : 1 → 2

- Les agents personnalisés passent de `CodeEnvironment` à `Agent`, avec changements de namespaces/contrats ; la clé `code_environment` devient `agents`.
- Les options `--ignore-guidelines` / `--ignore-mcp` sont remplacées par `--guidelines` / `--skills` / `--mcp`. Aucun agent personnalisé identifié dans le dépôt.
- Relancer `php artisan boost:install` et examiner les instructions générées. Le script Composer `post-update-cmd` appelle `boost:update` : vérifier ce parcours lors de la migration.

Source : [guide Boost 2](https://github.com/laravel/boost/blob/main/UPGRADE.md).

### laravel/tinker : 2 → 3

- Le support des anciens PHP est retiré : le prérequis actuel PHP 8.4 convient.
- Les exceptions donnent désormais un code de sortie correct. Les automatisations utilisant `artisan tinker --execute` peuvent donc échouer là où elles semblaient réussir. Aucun usage de ce type identifié dans les scripts Composer.

Source : [release Tinker 3](https://github.com/laravel/tinker/releases/tag/v3.0.0).

### mallardduck/blade-lucide-icons : 1 → 2

Le changelog consulté ne fournit pas de guide complet de rupture pour 2.0.0 ; il signale de nombreuses modifications de SVG en 2.x. Aucun renommage précis de composant utilisé par l'application n'est confirmé par cet audit. Vérifier les composants `x-lucide-*` dans `resources/views`, notamment pagination, contact et media-kit : présence des icônes et rendu visuel. Traiter cette majeure comme une migration à vérifier, sans supposer une compatibilité garantie.

Sources : [changelog officiel](https://github.com/mallardduck/blade-lucide-icons/blob/main/CHANGELOG.md), [documentation du package](https://github.com/mallardduck/blade-lucide-icons/blob/main/README.md).

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

Les versions cibles autres que Glide n'ont pas été installées ni validées fonctionnellement. Le blocage DKIM de la simulation initiale a été levé par le retrait du paquet ; une nouvelle simulation globale reste à effectuer.
