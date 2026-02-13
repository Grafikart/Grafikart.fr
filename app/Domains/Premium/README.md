# Domain Premium

Gestion de l'abonnement premium, des paiements (Stripe et PayPal) et du suivi des transactions.

## Schema BDD

Le systeme repose sur trois tables. 

- Les `plans` definissent les formules d'abonnement (prix, duree).
- Les `transactions` enregistrent chaque paiement effectue.
- Les `subscriptions` suivent les abonnements Stripe recurrents. 

L'état premium de l'utilisateur est stocké directement sur le modele `User` via `premium_end_at`, ce qui permet d'empiler les durées en cas de renouvellement anticipé.

```mermaid
erDiagram
    plans {
        bigint id PK
        varchar name "Mensuel, Annuel..."
        int price "en euros"
        int duration "en mois"
        varchar stripe_id "price_xxx"
    }
    transactions {
        bigint id PK
        bigint user_id FK
        int duration "en mois"
        int price "en centimes"
        int tax "TVA en centimes"
        int fee "frais plateforme"
        varchar method "stripe | paypal"
        varchar method_id "ID fournisseur"
        timestamp refunded_at "nullable"
        varchar firstname
        varchar lastname
        varchar address
        varchar postal_code
        varchar country_code
        timestamp created_at
    }
    subscriptions {
        bigint id PK
        bigint user_id FK "nullable, nullOnDelete"
        bigint plan_id FK
        smallint state "0=inactif, 1=actif"
        timestamp next_payment "nullable"
        varchar stripe_id "sub_xxx, unique"
    }
    users {
        bigint id PK
        varchar stripe_id "cus_xxx"
        timestamp premium_end_at "nullable"
    }
    transactions }o--|| users : "user_id"
    subscriptions }o--|| users : "user_id"
    subscriptions }o--|| plans : "plan_id"
```

## Paiement unique (Stripe)

L'utilisateur choisit un plan et clique sur le bouton Stripe. Le frontend envoie une requete au `PremiumController` qui cree une session Checkout et renvoie l'URL de redirection. Apres paiement, Stripe envoie un webhook qui declenche la chaine de creation de transaction et d'extension du premium.

```mermaid
sequenceDiagram
    participant User as Utilisateur
    participant Front as Frontend
    participant API as PremiumController
    participant Stripe as Stripe API
    participant Webhook as StripeWebhookController
    participant Sub as PaymentSubscriber
    participant DB as Database

    User->>Front: Clique "Payer avec Stripe"
    Front->>API: POST /api/premium/{plan}/stripe?subscription=0
    API->>Stripe: createPaymentSession(user, plan)
    Stripe-->>API: Session (url)
    API-->>Front: {url}
    Front->>Stripe: Redirection vers Checkout
    Stripe->>Stripe: Paiement CB
    Stripe->>Webhook: webhook (payment_intent.succeeded)
    Webhook->>Webhook: PaymentEvent
    Webhook->>Sub: onPayment()
    Sub->>DB: Transaction::create()
    Sub->>DB: user.premium_end_at += plan.duration
    Sub->>Sub: PremiumSubscriptionEvent
    Stripe-->>Front: Redirection ?success=1
```

## Abonnement recurrent (Stripe)

Le flux est similaire au paiement unique, mais utilise le mode `subscription` de Checkout. Stripe envoie des webhooks supplémentaires pour gerer le cycle de vie de l'abonnement (creation, mise à jour, suppression). À chaque renouvellement, un `payment_intent.succeeded` déclenche une nouvelle transaction.

```mermaid
sequenceDiagram
    participant User as Utilisateur
    participant API as PremiumController
    participant Stripe as Stripe
    participant Webhook as StripeWebhookController
    participant DB as Database

    User->>API: POST /api/premium/{plan}/stripe?subscription=1
    API->>Stripe: createSubscriptionSession(user, plan)
    Stripe-->>User: Checkout (mode=subscription)
    User->>Stripe: Paiement CB

    Stripe->>Webhook: customer.subscription.created
    Webhook->>DB: Subscription::create(state=ACTIVE)

    Stripe->>Webhook: payment_intent.succeeded
    Webhook->>DB: Transaction + extend premium_end_at

    Note over Stripe,Webhook: Lors du renouvellement
    Stripe->>Webhook: payment_intent.succeeded
    Webhook->>DB: Nouvelle Transaction + extend premium

    Note over Stripe,Webhook: Annulation par l'utilisateur
    Stripe->>Webhook: customer.subscription.updated (cancel_at_period_end)
    Webhook->>DB: subscription.state = INACTIVE

    Note over Stripe,Webhook: Fin de periode
    Stripe->>Webhook: customer.subscription.deleted
    Webhook->>DB: Subscription::delete()
```

## Paiement PayPal

Pour PayPal, le paiement est initie cote client via le SDK PayPal. L'utilisateur choisit son pays (pour le calcul de TVA : 20% France, 0% ailleurs) puis valide dans la popup PayPal. Le frontend envoie ensuite l'`orderId` au backend qui capture le paiement et dispatche le meme `PaymentEvent` que Stripe.

```mermaid
sequenceDiagram
    participant User as Utilisateur
    participant Front as Frontend (PayPal SDK)
    participant PayPal as PayPal API
    participant API as PremiumController
    participant Sub as PaymentSubscriber
    participant DB as Database

    User->>Front: Selectionne PayPal + pays
    Front->>PayPal: Cree l'ordre (SDK client)
    PayPal-->>User: Popup d'approbation
    User->>PayPal: Approuve le paiement
    Front->>API: POST /api/premium/paypal/{orderId}
    API->>PayPal: capture(orderId)
    PayPal-->>API: Payment capture
    API->>Sub: PaymentEvent
    Sub->>DB: Transaction::create()
    Sub->>DB: user.premium_end_at += plan.duration
```

## Remboursement

Le remboursement peut etre declenche depuis le CMS (marquage manuel) ou automatiquement via un webhook Stripe (`charge.refunded`). Dans les deux cas, le `PaymentRefundedSubscriber` retrouve la transaction, soustrait la durée du premium de l'utilisateur et marque la transaction comme remboursée.

```mermaid
sequenceDiagram
    participant Source as Stripe webhook / CMS Admin
    participant Webhook as StripeWebhookController
    participant Sub as PaymentRefundedSubscriber
    participant DB as Database

    Source->>Webhook: charge.refunded
    Webhook->>Webhook: PaymentRefundedEvent
    Webhook->>Sub: onPaymentRefunded()
    Sub->>DB: Trouve Transaction par method_id
    Sub->>DB: user.premium_end_at -= transaction.duration
    Sub->>DB: transaction.refunded_at = now()
    Sub->>Sub: PremiumCancelledEvent
```

## Graphiques du CMS (revenus)

Le `TransactionRepository` fournit les donnees agregees pour le dashboard CMS. Il calcule le revenu net (`price - tax - fee`) en excluant les transactions remboursees, avec deux granularites : journaliere (30 derniers jours) et mensuelle (24 derniers mois).

```mermaid
flowchart LR
    A[DashboardController] -->|getDailyRevenues| B[TransactionRepository]
    A -->|getMonthlyRevenues| B
    B -->|SUM price - tax - fee| C[(transactions)]
    C -->|exclut refunded_at| D[DailyData / MonthlyData]
    D --> E[Graphiques CMS]
```

## Vérification du statut premium

Le statut premium est vérifié via `User::isPremium()` qui compare `premium_end_at` a la date courante. Une route dédiée (`/auth/check/premium`) permet à un reversé proxy (Caddy/Nginx) de verifier le statut via `forward_auth` ou `auth_request`.

```mermaid
flowchart TD
    A[Requete utilisateur] --> B{User::isPremium?}
    B -->|premium_end_at > now| C[204 - Acces autorise]
    B -->|null ou passe| D[403 - Acces refuse]

    E[Caddy / Nginx] -->|forward_auth /auth/check/premium| B
```
