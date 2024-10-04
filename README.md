# Grafikart.New

[![Tests](https://github.com/Grafikart/Grafikart.fr/workflows/Tests/badge.svg)](https://github.com/Grafikart/Grafikart.fr/actions?query=workflow%3ATests)
[![SymfonyInsight](https://insight.symfony.com/projects/0aed16f6-8916-4755-be7f-4adcadca72fe/mini.svg)](https://insight.symfony.com/projects/0aed16f6-8916-4755-be7f-4adcadca72fe)

<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Licence Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" /></a>

Dépôt pour la nouvelle version de Grafikart.fr. L'objectif est de rendre le projet Open Source afin que tout le monde puisse participer à l'élaboration du site et à son évolution.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Participer (faire une PR)](#participer-faire-une-pr)
- [Objectifs, pourquoi une refonte ?](#objectifs-pourquoi-une-refonte-)
  - [Problèmes techniques](#probl%C3%A8mes-techniques)
  - [Problème d'organisation / d'UX](#probl%C3%A8me-dorganisation--dux)
  - [Rendre le code Open Source](#rendre-le-code-open-source)
- [Design](#design)
- [Tips](#tips)
- [Fonts à tester](#fonts-%C3%A0-tester)
- [Référence](#r%C3%A9f%C3%A9rence)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## MR à suivre :

- Erreur dans les logs de PHPUnit : https://github.com/symfony/monolog-bundle/pull/336
- Plusieurs listeners Doctrine ne peuvent pas utiliser la même class : https://github.com/doctrine/DoctrineBundle/issues/1224
- Twig utilise yield, mettre à jour le CacheNode pour gérer ce nouveau système

## Participer (faire une PR)

Le développement a commencé et vous pouvez récupérer le projet et pour travailler dessus. Afin de simplifier la mise en place de l'environnement de dev, **docker** a été choisi :

```bash
make dev ## Permet de lancer le serveur de développement, accessible ensuite sur http://grafikart.localhost:8000
make seed ## Permet de remplir la base de données
```

Pour les tests vous pouvez lancer une de ces commandes :

```bash
make test ## Permet de lancer les tests
make tt ## Permet de relancer les tests automatiquement
make lint ## Permet de vérifier que le code ne contienne pas d'erreur
```

### Rendre le code Open Source

La version actuelle du site contient beaucoup de choses en dur ce qui empêche le code d'être partagé sans risque. L'objectif de cette version est donc de créer un code qui puisse être utilisé et lancé facilement par les personnes qui souhaitent collaborer.

## Design

Pour le design j'utilise [Figma](https://www.figma.com) car c'est l'outil le plus simple à utiliser pour collaborer rapidement.

- [Maquettes Figma](https://www.figma.com/file/HnamCOnYf7eWZCtRIru5o1/Site?node-id=17%3A2)

## Tips

Lien de redirection pour l'oauth https://grafikart.fr/oauth/check/{github|google|facebook}

## Nginx config

https://www.digitalocean.com/community/tools/nginx?domains.0.server.domain=test.grafikart.fr&domains.0.server.path=%2Fhome%2Fgrafikart%2Ftest.grafikart.fr&domains.0.logging.accessLog=true&domains.0.logging.errorLog=true&global.security.limitReq=true&global.php.phpServer=%2Fvar%2Frun%2Fphp%2Fphp7.4-fpm.sock&global.logging.accessLog=%2Fvar%2Flog%2Fnginx%2Faccess.log%20warn&global.logging.errorLog=%2Fvar%2Flog%2Fnginx%2Ferror.log%20warn%20warn

