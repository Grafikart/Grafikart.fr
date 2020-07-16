<?php

namespace App\Domain\Forum\Subscriber;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Forum\Repository\MessageRepository;
use App\Domain\Forum\Repository\TopicRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForumSubscriber implements EventSubscriberInterface
{
    private MessageRepository $messageRepository;
    private TopicRepository $topicRepository;

    public function __construct(TopicRepository $topicRepository, MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->topicRepository = $topicRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserBannedEvent::class => 'cleanUserContent',
        ];
    }

    public function cleanUserContent(UserBannedEvent $event): void
    {
        $this->messageRepository->deleteForUser($event->getUser());
        $this->topicRepository->deleteForUser($event->getUser());
    }
}
