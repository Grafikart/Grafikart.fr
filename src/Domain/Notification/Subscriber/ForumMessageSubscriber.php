<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForumMessageSubscriber implements EventSubscriberInterface
{

    private NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageCreatedEvent::class => ['onMessageCreated']
        ];
    }

    public function onMessageCreated(MessageCreatedEvent $event): void
    {
        $name = $event->getMessage()->getAuthor()->getUsername();
        $topic = $event->getMessage()->getTopic();
        $user = $topic->getAuthor();
        $this->service->notifyUser($user, "**$name** a répondu à votre sujet {$topic->getName()}", $topic);
    }
}
