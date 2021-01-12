# Grafikart.New

[![Build Status](https://travis-ci.org/Grafikart/Grafikart.fr.svg?branch=master)](https://travis-ci.org/Grafikart/Grafikart.fr)
[![Tests](https://github.com/Grafikart/Grafikart.fr/workflows/Tests/badge.svg)](https://github.com/Grafikart/Grafikart.fr/actions?query=workflow%3ATests)
[![SymfonyInsight](https://insight.symfony.com/projects/0aed16f6-8916-4755-be7f-4adcadca72fe/mini.svg)](https://insight.symfony.com/projects/0aed16f6-8916-4755-be7f-4adcadca72fe)

<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Licence Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" /></a>

Dépôt pour la nouvelle version de Grafikart.fr. L'objectif est de rendre le projet Open Source afin que tout le monde puisse participer à l'élaboration du site et à son évolution.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Etat d'avancement](#etat-davancement)
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

## Etat d'avancement

L'avancement peut être suivi sur la [board trello de grafikart](https://trello.com/b/oKnfpVtU/grafikart)

## MR à suivre :

- Erreur dans les logs de PHPUnit : https://github.com/symfony/monolog-bundle/pull/336

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

## Objectifs, pourquoi une refonte ?

Le site actuel marche correctement alors pourquoi se lancer dans un nouveau développement ?

### Problèmes techniques

Le site a été développé il y a un moment à une époque où j'apprenais Ruby on Rails. Le code de base n'est donc pas idéal, mais les problèmes se sont aussi accumulés lorsque j'ai tenté de passer une partie de la base de données sur Neo4j. Il n'existe pas forcément de bons drivers sur Ruby et certaines requêtes sont trop complexes à mes yeux et posent des problèmes de performance lors de l'agrégation des contenus (et je n'utilise pas au final les possibilités offertes par neo4j).

### Problème d'organisation / d'UX

Les contenus ne sont pas correctement mis en avant et il n'est pas évident pour un nouvel utilisateur de trouver les bons contenus.

- Par quelle vidéo dois-je commencer ?
- Quelles formations sont disponibles (peu de gens savent qu'il existe une formation sur la mise en place de serveur par exemple).
- Les commentaires ne servent pas forcément à grand-chose en l'état (remplacer peut-être par un système de questions ?).
- Les contenus premiums ne sont pas forcément mis en avant et on ne sait pas trop ce qui est disponible et ce qui ne l'est pas.
- Un système de progression doit être mis en place pour permettre de reprendre une formation ou une vidéo.
- Le système de pricing n'est pas clair, on a les mêmes fonctionnalités pour 3.5€,10€,37€ et la seule différence est marquée en gris en haut à droite (temps d'abonnement).

### Rendre le code Open Source

La version actuelle du site contient beaucoup de choses en dur ce qui empêche le code d'être partagé sans risque. L'objectif de cette version est donc de créer un code qui puisse être utilisé et lancé facilement par les personnes qui souhaitent collaborer.

## Design

Pour le design j'utilise [Figma](https://www.figma.com) car c'est l'outil le plus simple à utiliser pour collaborer rapidement.

- [Maquettes Figma](https://www.figma.com/file/HnamCOnYf7eWZCtRIru5o1/Site?node-id=17%3A2)

## Tips

Lien de redirection pour l'oauth https://grafikart.fr/oauth/check/{github|google|facebook}

Gérer l'opacité des couleurs :

```
0%          00
5%          0C
10%         19
15%         26
20%         33
25%         3F
30%         4C
35%         59
40%         66
45%         72
50%         7F
55%         8C
60%         99
65%         A5
70%         B2
75%         BF
80%         CC
85%         D8
90%         E5
95%         F2
100%        FF
```

### Vider le cache

```
php bin/console cache:pool:clear cache.global_clearer
php bin/console cache:clear
```

## Nginx config

https://www.digitalocean.com/community/tools/nginx?domains.0.server.domain=test.grafikart.fr&domains.0.server.path=%2Fhome%2Fgrafikart%2Ftest.grafikart.fr&domains.0.logging.accessLog=true&domains.0.logging.errorLog=true&global.security.limitReq=true&global.php.phpServer=%2Fvar%2Frun%2Fphp%2Fphp7.4-fpm.sock&global.logging.accessLog=%2Fvar%2Flog%2Fnginx%2Faccess.log%20warn&global.logging.errorLog=%2Fvar%2Flog%2Fnginx%2Ferror.log%20warn%20warn

## Fonts à tester

- Sofia Pro
- Bruta Pro

## Référence

Pour évaluer l'efficacité de la nouvelle version :

- application.js version actuelle : 174ko / 490ko
- application.css version actuelle : 32ko / 131ko

## Docker ref

- curl -fsSL https://get.docker.com/rootless | sh
- systemctl --user enable docker.service
