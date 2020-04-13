# Structure du projet

- **/assets**, contient les assets JavaScript / CSS liées au projets. Ces assets sont compilé dans le dossier **public/assets**.
- **/config**, contient la configuration du projet. Cette configuration correspond au framework Symfony.
- **/docs**, contient cette documentation.
- **/fixtures**, gère les données qui sont utilisées pour remplir la base de données de dev.
- **/public**, est la racine du serveur web
- **/src**, contient les sources PHP (Symfony du projet)
- **/templates**, contient les vues (Twig)
- **/tools**, contient la configuration des outils liés à grafikart (et la configuration docker).

## Structure Symfony

Le dossier **/src** ne ressemble pas forcément à la structure que l'on peut attendre d'un projet Symfony. Cette structure correspond à une structure qui est un mélange de différentes approches (en s'inspirant du principe de [contexte](https://hexdocs.pm/phoenix/contexts.html), ddd, [architecture hexagonale](https://blog.octo.com/architecture-hexagonale-trois-principes-et-un-exemple-dimplementation/) sans forcément les respecter à la lettre).

- **Command**, contient les commandes qui permettent d'intéragir avec le système depuis le CLI.
- **Http**, contient les classes qui permettent d'intéragir avec le système depuis des appels HTTP.
- **Core**, contient les classes qui sont génériques (ajout de fonctions à Twig, ajout de type de formulaires...) et qui n'ont pas nécessairement leur place ailleurs.
- **Domain**, contient les classes qui permettent de gérer la logique métier de l'application. Ces classes doivent être tant que possible indépendante et isolées les unes des autres. La communication avec les domaines peut se faire de 3 façons :
    - Via un **Service** qui contient les méthodes qui seront utilisé depuis la couche HTTP / Command
    - Via un **système d'évènement**
    - Via un **repository** qui permettra au controllers ou au command de récupérer les informations nécessaires.
- **Infrastructure**, définit les élément d'infrastructure qui permet au domaine de communiquer avec le système (couche de persistence fichier, envoie d'emails, base de données...).

### Pourquoi ne pas utiliser la structure de Symfony ?

Cette structure peut sembler bizarre mais elle fait suite à l'expérience que j'ai pu avoir avec d'autres projets.

Par défaut les frameworks groupent les classes en fonction de leur rôle (controller, entité, repository, events...). Ce découpage est logique pour un petit projet mais s'avère très rapidement pénible lorsque l'on travaille sur un projet plus conséquent (Tout ce qui permet de faire fonctionner le blog se retrouve éclater dans différents dossiers).

La couche HTTP est en fait une interface qui permet de communiquer avec votre système. Cette interface peut être amenée à changer (API, GraphQL, Command...) du coup il est impératif selon moi d'essayer d'éviter le code métier dans les controllers (ils vont s'occuper de valider les données puis c'est le système qui s'occupe du reste).

Le principe des domaines s'inspire de l'architecture hexagonale même si le découplage n'est pas aussi fort que l'approche originale qui fait intervenir la notion d'**Adapters** et de **Port**. Ce niveau d'abstraction ne me semble pas nécessaire ici. L'EntityManager et les controller joueront le rôle des adapters, et les Service / EventSubscriber joueront le rôle des ports.
