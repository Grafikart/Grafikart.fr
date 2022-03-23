<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Notification\NotificationService;
use App\Domain\Revision\Event\RevisionAcceptedEvent;
use App\Domain\Revision\Event\RevisionRefusedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RevisionSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RevisionAcceptedEvent::class => 'onRevisionAccepted',
            RevisionRefusedEvent::class  => 'onRevisionRefused'
        ];
    }

    public function onRevisionAccepted(RevisionAcceptedEvent $revisionAcceptedEvent): void
    {
        $revision = $revisionAcceptedEvent->getRevision();
        $this->notificationService->notifyUser(
            $revision->getAuthor(),
            sprintf("Votre modification pour l'article <strong>%s</strong> a été acceptée",
                $revision->getTarget()->getTitle()),
            $revision);
    }

    public function onRevisionRefused(RevisionRefusedEvent $revisionAcceptedEvent): void
    {
        $revision = $revisionAcceptedEvent->getRevision();
        $this->notificationService->notifyUser(
            $revision->getAuthor(),
            sprintf("Votre modification pour l'article <strong>%s</strong> a été refusée :(",
                $revision->getTarget()->getTitle()),
            $revision);
    }
}
