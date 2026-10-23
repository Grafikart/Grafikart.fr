# Grafikart.New

[![Tests](https://github.com/Grafikart/Grafikart.fr/workflows/tests/badge.svg)](https://github.com/Grafikart/Grafikart.fr/actions?query=workflow%3Atests)

<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Licence Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" /></a>

Dépôt pour la nouvelle version de Grafikart.fr. L'objectif est de rendre le projet Open Source afin que tout le monde puisse participer à l'élaboration du site et à son évolution.

## Participer (faire une PR)

Le développement a commencé et vous pouvez récupérer le projet et pour travailler dessus. Afin de simplifier la mise en place de l'environnement de dev, **docker** a été choisi :

```bash
make dev ## Permet de lancer le serveur de développement, accessible ensuite sur http://grafikart.localhost:8000
make seed ## Permet de remplir la base de données
```

## Sentry

Les exceptions Laravel sont envoyées à Sentry lorsque `SENTRY_LARAVEL_DSN` est renseigné dans l'environnement. Récupérez le DSN dans les paramètres du projet Sentry de l'organisation `grafikart` (Client Keys), puis ajoutez-le au `.env` du serveur :

```dotenv
SENTRY_LARAVEL_DSN=https://PUBLIC_KEY@HOST/PROJECT_ID
SENTRY_SEND_DEFAULT_PII=false
SENTRY_TRACES_SAMPLE_RATE=0
```

Après modification, reconstruisez le cache de configuration (`php artisan config:cache`) et redémarrez les workers de queue. Vérifiez l'envoi d'un événement avec `php artisan sentry:test`, puis consultez les issues du projet Sentry. Les traces de performance sont désactivées ; les exceptions sont capturées automatiquement et les exceptions interceptées peuvent être signalées avec `report($exception)`.

## Tips

Lien de redirection pour l'oauth https://grafikart.fr/oauth/check/{github|google|facebook}
