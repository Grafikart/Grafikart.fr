@extends('pages.layout')

@section('title', "Conditions générales de vente")

@section('content')
    <p>
        En utilisant le site Grafikart.fr et en souscrivant à un abonnement premium, vous acceptez sans réserve les
        présentes conditions générales d'utilisation. Le site est édité et exploité par l'EURL Boyer Jonathan, domiciliée
        en France. Pour toute question, vous pouvez nous contacter via le
        <a href="{{ route('contact') }}">formulaire de contact</a>.
    </p>
    <h2>Vos responsabilités</h2>
    <p>
        Vous vous engagez à fournir des informations exactes lors de la création de votre compte et à les maintenir à
        jour. Vous êtes responsable de la confidentialité de vos identifiants et de toute activité réalisée depuis
        votre compte.
    </p>
    <p>
        L'utilisation du service à des fins illégales, frauduleuses ou portant atteinte aux droits de tiers est
        strictement interdite. Le partage de compte ou la revente d'un accès premium est également interdit et
        entraînera la résiliation immédiate de l'abonnement.
    </p>
    <p>
        Vous vous engagez à utiliser le service de manière responsable, sans porter atteinte à l'intégrité de la plateforme.
        L'utilisation abusive du site (téléchargement en masse, trop grande utilisation de la bande passante...) pourra entrainer une suspension de service (temporaire dans un premier temps, puis définitive en cas de récidive).
    </p>
    <h2>Nos services</h2>
    <p>
        Grafikart.fr propose des tutoriels vidéo gratuits et un abonnement premium donnant accès à des contenus
        exclusifs : téléchargement des vidéos, accès aux fichiers sources et tout autre avantage décrit sur la
        <a href="{{ route('premium') }}">page premium</a>.
    </p>
    <p>
        Nous nous efforçons de produire des contenus qui vous permettront d'apprendre à votre rythme.
        Cependant la nature même des contenus fait que l'apprentissage ne peut être adapté à toutes les personnes.
        N'hésitez pas, si les contenus ne vous conviennent pas, à utiliser le <a href="{{ route('contact') }}">formulaire de contact</a> pour nous faire des retours et permettre l'amélioration de la plateforme.
    </p>
    <p>
        Nous nous réservons le droit de faire évoluer les contenus et les fonctionnalités du service sans préavis.
        Aussi, la nature du site fait que nous ne pouvons pas garantir une disponibilité continue du service mais nous nous efforçons de faire notre maximum pour limiter les temps d'interruption. En cas d'interruption prolongée (plus de 3 heures), la durée de votre compte premium sera automatiquement adaptée pour prendre en compte l'interruption du service.
    </p>
    <h2>Licence</h2>
    <p>
        L'abonnement premium est strictement personnel et non-transférable. Il vous est accordé une licence d'usage
        individuel sur les contenus accessibles. Toute reproduction, redistribution, diffusion publique ou mise à
        disposition des contenus à des tiers, qu'elle soit gratuite ou payante, est interdite sans autorisation
        écrite préalable.
    </p>
    <h2>Paiement</h2>
    <p>
        Les prix sont indiqués en euros toutes taxes comprises (TTC). Le paiement est réalisé en ligne de manière
        sécurisée via Stripe ou Paypal. Grafikart.fr ne stocke à aucun moment vos coordonnées bancaires.
    </p>
    <p>
        En souscrivant à un abonnement, vous autorisez le prélèvement automatique du montant correspondant à chaque
        échéance (mensuelle ou annuelle selon la formule choisie). L'accès au contenu premium est activé
        immédiatement après validation du paiement.
    </p>
    <h2>Remboursements</h2>
    <p>
        Conformément à
        <a href="https://www.legifrance.gouv.fr/codes/article_lc/LEGIARTI000044563170?fonds=CODE&amp;init=true&amp;query=L221-28&amp;searchField=ALL&amp;tab_selection=code" target="_blank" rel="noopener">l'article L221-28 du Code de la consommation</a>,
        le droit de rétractation ne s'applique pas aux contenus numériques dont l'accès a débuté avec votre accord
        avant l'expiration du délai légal de 14 jours. En accédant immédiatement aux contenus premium après
        souscription, vous renoncez expressément à ce droit.
    </p>
    <p>
        Cependant, si vous n'êtes pas satisfait, vous pouvez nous contacter via le
        <a href="{{ route('contact') }}">formulaire de contact</a>. Chaque demande sera étudiée individuellement.
    </p>
    <h2>Confidentialité</h2>
    <p>
        Les données collectées lors de la souscription (nom, adresse e-mail, historique de facturation) sont
        utilisées uniquement pour la gestion de votre abonnement. Elles ne sont pas transmises à des tiers, à
        l'exception de Stripe et Paypal dans le cas d'un paiement. Pour plus d'informations, consultez notre
        <a href="{{ route('pages.privacy') }}">politique de confidentialité</a>.
    </p>
    <h2>Résiliation</h2>
    <p>
        Vous pouvez résilier votre abonnement à tout moment depuis <a href="{{ route('users.edit') }}">votre
            compte</a>. La résiliation prend effet à la fin de la période en cours déjà réglée (aucun remboursement
        prorata temporis n'est effectué pour la période restante).
    </p>
    <p>
        Grafikart.fr se réserve le droit de résilier un abonnement ou de suspendre un compte pour toute raison,
        notamment en cas de non-respect des présentes conditions. Si le compte a un abonnement actif, la résiliation / suspension sera faite à la fin de la période en cours déjà réglée.
    </p>
    <h2>Modifications du tarif</h2>
    <p>
       Les tarifs sont susceptibles d'évoluer dans le temps, ces changements ne sont pas rétro-actifs et tout compte déjà souscrit gardera sa tarification d'origine.
    </p>
    <p>
        En cas de fermeture définitive du service premium, les abonnés actifs seront remboursés au prorata de la
        période restante.
    </p>
    <h2>Propriété intellectuelle</h2>
    <p>
        L'ensemble des contenus présents sur Grafikart.fr (vidéos, textes, images, code source des tutoriels) est
        protégé par le droit d'auteur. Toute reproduction ou diffusion sans autorisation explicite est interdite.
    </p>
@endsection
