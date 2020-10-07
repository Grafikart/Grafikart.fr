<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Forum\TopicService;
use App\Domain\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForumMessageSubscriber implements EventSubscriberInterface
{
    private NotificationService $service;
    private TopicService $topicService;

    public function __construct(NotificationService $service, TopicService $topicService)
    {
        $this->service = $service;
        $this->topicService = $topicService;
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageCreatedEvent::class => ['onMessageCreated'],
        ];
    }

    public function onMessageCreated(MessageCreatedEvent $event): void
    {
        $message = $event->getMessage();
        $name = $message->getAuthor()->getUsername();
        $topic = $message->getTopic();

        // On récupère les utilisateurs à notifier
        $users = $this->topicService->usersToNotify($event->getMessage());

        foreach ($users as $user) {
            $this->service->notifyUser($user, "**$name** a répondu à votre sujet {$topic->getName()}", $message);
        }
    }
}
