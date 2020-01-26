# Grafikart.New

[![Build Status](https://travis-ci.org/Grafikart/Grafikart.fr.svg?branch=master)](https://travis-ci.org/Grafikart/Grafikart.fr)
<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Licence Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" /></a>

Dépôt pour la nouvelle version de Grafikart.fr. L'objectif est de rendre le projet Open Source afin que tout le monde puisse participer à l'élaboration du site et à son évolution.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Pourquoi une refonte ?](#pourquoi-une-refonte-)
- [Etat d'avancement](#etat-davancement)
- [Objectifs, pourquoi une refonte ?](#objectifs-pourquoi-une-refonte-)
  - [Problèmes techniques](#probl%C3%A8mes-techniques)
  - [Problème d'organisation / d'UX](#probl%C3%A8me-dorganisation--dux)
  - [Rendre le code Open Source](#rendre-le-code-open-source)
- [Design](#design)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Etat d'avancement

- [x] Créer le dépôt Github
- [ ] Trouver une licence pour ce dépôt (qui interdit l'utilisation commerciale)
- [ ] Créer le design du site et des différentes pages
- [ ] Intégration du design
- [ ] Développement 
- [ ] Déploiement

## Objectifs, pourquoi une refonte ?

Le site actuel marche correctement alors pourquoi se lancer dans un nouveau développement ?

### Problèmes techniques

Le site a été développé il y a un moment à une époque ou j'apprenais Ruby on Rails. Le code de base n'est donc pas idéal mais les problèmes se sont aussi accumulés lorsque j'ai tenté de passer une partie de la base de données sur Neo4j. Il n'existe pas forcément de bons driver sur Ruby et certaines requêtes sont trop complexes à mes yeux et posent des problèmes de performance lors de l'aggrégation des contenus (et je n'utilise pas au final les possibilités offertes par neo4j).

### Problème d'organisation / d'UX

Les contenus ne sont pas correctement mis en avant et il n'est pas évident pour un nouvel utilisateur de trouver les bons contenus.

- Par quelle vidéo dois-je commencer ?
- Quelles formations sont disponibles (peu de gens savent qu'il existe une formation sur la mise en place de serveur par exemple). 
- Les commentaires ne servent pas forcément à grand chose en l'état (remplacer peut être par un système de questions ?).
- Les contenus premiums ne sont pas forcément mis en avant et on ne sait pas trop ce qui est disponible et ce qui ne l'est pas.
- Un système de progression doit être mis en place pour permettre de reprendre une formation ou une vidéo.
- Le système de pricing n'est pas clair, on a les même fonctionnalités pour 3.5€,10€,37€ et la seule différence est marquée en gris en haut à droite (temps d'abonnement)
### Rendre le code Open Source

La version actuelle du site contient beaucoup de chose en dur ce qui empèche le code d'être partagé sans risque. L'objectif de cette version est donc de créer un code qui puisse être utilisé et lancé facilement par les personnes qui souhaitent collaborer.

## Design

Pour le design j'utilise [Figma](https://www.figma.com) car c'est l'outil le plus simple à utiliser pour collaborer rapidement.

- [Maquettes Figma](https://www.figma.com/file/HnamCOnYf7eWZCtRIru5o1/Site?node-id=17%3A2)
