@extends('pages.layout')

@section('title', 'Politique de confidentialité')

@section('content')
    <h2>
        Utilisation des données personnelles collectées
    </h2>
    <h3>
        Cookies
    </h3>
    <p>
        Si vous avez un compte et que vous vous connectez sur le site, un cookie temporaire sera créé afin de persister
        votre état de connexion. Ce cookie sera automatiquement détruit lorsque vous vous déconnectez du site.
    </p>
    <h3>
        Contenu embarqué depuis d’autres sites
    </h3>
    <p>
        Certains articles de ce site peuvent inclure des contenus intégrés (par exemple des vidéos, images, articles…).
        Le contenu intégré depuis d’autres sites se comporte de la même manière que si le visiteur se rendait sur cet
        autre site.
    </p>
    <p>
        Ces sites web pourraient collecter des données sur vous, utiliser des cookies, embarquer des outils de suivi
        tiers, suivre vos interactions avec ces contenus embarqués si vous disposez d’un compte connecté sur leur site
        web.
    </p>
    <h3>
        Statistiques et mesures d’audience
    </h3>
    <p>
        Afin d'analyser le traffic du site (nombre de visites, pages les plus consultées...) nous n'utilisons pas de
        service tiers mais un service auto hébergé basé sur <a href="https://github.com/usefathom/fathom">Fathom</a>.
        Les données sont anonymisées et ne permettent pas d'identifier le comportement individuel d'un utilisateur mais
        plutôt une tendance générale (nombre de visites sur le site et le nombre de visites par page seulement).
    </p>
    <hr>
    <h2>
        Durées de stockage de vos données
    </h2>
    <p>
        Si vous laissez un commentaire, le commentaire et ses métadonnées sont conservés indéfiniment. Cela permet de
        reconnaître et approuver automatiquement les commentaires suivants au lieu de les laisser dans la file de
        modération.
    </p>
    <p>
        Pour les utilisateurs et utilisatrices qui s’enregistrent sur le site, nous stockons également les données
        personnelles indiquées dans leur profil. Tous les utilisateurs et utilisatrices peuvent voir, modifier ou
        supprimer leurs informations personnelles à tout moment. Seul le gestionnaire du site peut aussi voir et
        modifier ces informations.
    </p>
    <hr>
    <h2>
        Les droits que vous avez sur vos données
    </h2>
    <p>
        Si vous avez un compte ou si vous avez laissé des commentaires sur le site, vous pouvez demander la suppression
        des données personnelles vous concernant. Cela ne prend pas en compte les données stockées à des fins
        administratives, légales ou pour des raisons de sécurité.
    </p>
    <ul>
        <li>
            Si vous avez un compte et que vous souhaitez supprimer vos informations vous pouvez le faire automatiquement
            depuis <a href="{{ route('users.edit') }}">votre compte</a> en cliquant sur le bouton supprimé.
        </li>
        <li>
            Si vous avez posté un commentaire sans être connecté vous pouvez <a href="{{ route('contact') }}">me
                contacter</a> pour demander la suppression de ce dernier.
        </li>
    </ul>
    <hr>
    <h2>
        Transmission de vos données personnelles
    </h2>
    <p>
        Vos données ne sont pas partagées avec un tiers.
    </p>
    <hr>
    <h2>
        Informations supplémentaires
    </h2>
    <h3>
        Comment nous protégeons vos données
    </h3>
    <p>
        Afin que les données à caractère personnel que nous recueillons ne soient ni perdues, ni divulguées par des
        tiers non autorisés, nous avons mis en place les mesures nécessaires (pare feu, base de données non accessible à
        distance...) proportionnellement à la sensibilité des données stockées.
    </p>
    <h3>
        Procédures mises en œuvre en cas de fuite de données
    </h3>
    <p class="mb-20">
        Conformément à la procédure prévue par le règlement général sur la protection des données en cas de fuite ou
        d’anomalie concernant les données personnelles en notre possession, nous vous avertirons de la nature des
        données ayant fuitées et la nature du risque qui peut être engendrée, si cela peut entamer vos droits et
        libertés (données sensibles) dans un délai maximal de 72 heures après constat du problème.
    </p>
@endsection
