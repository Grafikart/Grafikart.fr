<?php

namespace App\Domains\Notification\Subscriber;

use App\Domains\Notification\NotificationService;
use App\Domains\Revision\Event\AcceptedRevisionEvent;
use App\Domains\Revision\Event\RejectedRevisionEvent;
use Illuminate\Events\Dispatcher;

class NotificationRevisionSubscriber
{
    public function __construct(private NotificationService $service) {}

    public function subscribe(Dispatcher $events): array
    {
        return [
            AcceptedRevisionEvent::class => 'onAccepted',
            RejectedRevisionEvent::class => 'onRejected',
        ];
    }

    public function onAccepted(AcceptedRevisionEvent $event): void
    {
        $revision = $event->revision;
        if (! $revision->user) {
            return;
        }

        $title = $revision->revisionable?->title ?? 'un contenu';

        $this->service->send(
            message: "Votre modification sur <strong>{$title}</strong> a été acceptée.",
            user: $revision->user,
            url: route('revisions.index'),
        );
    }

    public function onRejected(RejectedRevisionEvent $event): void
    {
        $revision = $event->revision;
        if (! $revision->user) {
            return;
        }

        $title = $revision->revisionable?->title ?? 'un contenu';

        $this->service->send(
            message: "Votre modification sur <strong>{$title}</strong> a été rejetée.",
            user: $revision->user,
            url: route('revisions.index'),
        );
    }
}
