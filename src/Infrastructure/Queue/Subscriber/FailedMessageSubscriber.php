<?php

namespace App\Infrastructure\Queue\Subscriber;

use App\Domain\Notification\NotificationService;
use App\Infrastructure\Queue\FailedJob;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

class FailedMessageSubscriber implements EventSubscriberInterface
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onMessageFailed',
        ];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        $stamp = new RedeliveryStamp(
            1,
            $event->getThrowable()->getMessage(),
        );
        $enveloppe = $event->getEnvelope()->with($stamp);
        $job = new FailedJob($enveloppe, 1);
        $this->notificationService->notifyChannel('admin', "Une tâche de la file d'attente a échoué", $job);
    }
}
