<?php

namespace App\Infrastructure\Spam\Subscriber;

use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Domain\Forum\Repository\TopicRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SpamSubscriber implements EventSubscriberInterface
{
    private TopicRepository $topicRepository;

    public function __construct(TopicRepository $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreTopicCreatedEvent::class => 'checkTopic',
        ];
    }

    public function checkTopic(PreTopicCreatedEvent $topicCreatedEvent): void
    {
        $topic = $topicCreatedEvent->getTopic();
        $topicCount = $this->topicRepository->countForUser($topic->getAuthor());
        if ($topicCount <= 3) {
            $topic->setSpam(true);
        }
    }
}
