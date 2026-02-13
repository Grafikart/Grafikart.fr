# Domain Notification

Gestion des notifications en temps reel pour les utilisateurs du site.

## Schema BDD

Une notification peut etre globale (`user_id = null`, visible par tous) ou ciblee vers un utilisateur specifique. Le champ `created_at` sert aussi de date de publication planifiee : une notification avec une date future ne sera ni visible ni broadcastee avant cette date.

```mermaid
erDiagram
    notifications {
        bigint id PK
        bigint user_id FK "nullable (null = globale)"
        varchar notifiable_type "morph vers Course/Formation"
        bigint notifiable_id
        varchar message "contenu HTML"
        varchar url
        varchar channel "public | admin | user/{id}"
        timestamp created_at "sert aussi de date planifiee"
    }
    users {
        bigint id PK
        timestamp notifications_read_at "date derniere lecture"
    }
    notifications }o--|| users : "user_id"
```

## Flux de creation

Quand un contenu (Course ou Formation) est publie avec une date future, le `NotificationContentSubscriber` detecte le changement et delegue au `NotificationService`. Celui-ci cree la notification en BDD (avec deduplication via `updateOrCreate`) puis dispatche un job en queue avec un delai correspondant a la date de publication. A l'echeance, le job broadcast l'evenement vers Mercure.

```mermaid
sequenceDiagram
    participant CMS as CMS / Admin
    participant Event as ContentCreated/UpdatedEvent
    participant Sub as NotificationContentSubscriber
    participant Svc as NotificationService
    participant DB as Database
    participant Job as NotificationBroadcasterJob
    participant Mercure as Hub Mercure

    CMS->>Event: Publication d'un contenu (Course/Formation)
    Event->>Sub: Dispatch evenement
    Sub->>Sub: Verifie : online=true && created_at future
    Sub->>Svc: send(message, model, date)
    Svc->>DB: updateOrCreate (deduplication URL + morph)
    Svc->>Job: dispatch() avec delay = created_at
    Note over Job: Attend la date planifiee
    Job->>Event: NotificationCreatedEvent
    Event->>Mercure: Broadcast sur topic "notification"
```

## Temps reel (Mercure)

Le `MercureSubscriberMiddleware` genere un JWT signe avec le `subscriberSecret` et le place dans un cookie `mercureAuthorization` scope sur le path `/.well-known/mercure`. Le navigateur utilise ce cookie pour authentifier l'`EventSource` SSE aupres du hub Mercure. Cote React, le hook `useNotifications` met a jour un atom Jotai a chaque evenement recu et joue un son de notification si la fenetre est active.

```mermaid
sequenceDiagram
    participant Browser as Navigateur
    participant Middleware as MercureSubscriberMiddleware
    participant Mercure as Hub Mercure
    participant React as useNotifications (React)

    Browser->>Middleware: Requete web (utilisateur connecte)
    Middleware->>Middleware: Genere JWT (subscribe: ["notification"])
    Middleware->>Browser: Cookie mercureAuthorization (path: /.well-known/mercure)

    Browser->>Mercure: EventSource (/.well-known/mercure?topic=notification)
    Note over Browser,Mercure: Auth via cookie JWT

    Mercure-->>React: SSE : NotificationCreatedEvent
    React->>React: Met a jour l'atom Jotai
    React->>Browser: Affiche la notification + joue un son
```

## Lecture

Lorsque l'utilisateur ouvre le menu de notifications, le frontend appelle `POST /api/notifications/read`. Le `NotificationService` enregistre la date de lecture dans `users.notifications_read_at`. Le compteur de non-lus est calcule cote client en comparant la date de chaque notification a ce `readAt`.

```mermaid
sequenceDiagram
    participant User as Utilisateur
    participant Menu as NotificationsMenu (React)
    participant API as POST /api/notifications/read
    participant Svc as NotificationService
    participant DB as Database

    User->>Menu: Ouvre le menu notifications
    Menu->>API: apiFetch (mark as read)
    API->>Svc: readAll(user)
    Svc->>DB: users.notifications_read_at = now()
    Svc->>Svc: event(NotificationReadEvent)
    Menu->>Menu: readAt mis a jour, compteur non-lus = 0
```

## Nettoyage

La commande `app:clean-notifications` est planifiee de facon hebdomadaire et supprime toutes les notifications de plus de 6 mois afin d'eviter l'accumulation de donnees obsoletes.

```mermaid
flowchart LR
    A[Schedule hebdomadaire] -->|app:clean-notifications| B[CleanNotificationsCommand]
    B -->|NotificationService::clean| C[Supprime les notifications > 6 mois]
```

## Scope `forUser`

Le scope `forUser` centralise la logique de visibilite des notifications pour un utilisateur donne. Il exclut les notifications planifiees dans le futur, ne retient que le channel `public`, et combine les notifications globales (`user_id IS NULL`) avec celles ciblees vers l'utilisateur.

- `created_at <= now()` (pas de notifications futures)
- `channel = 'public'`
- `user_id IS NULL` (globale) OU `user_id = user.id` (ciblee)
