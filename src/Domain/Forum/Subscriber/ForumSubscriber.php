<?php

namespace App\Domain\Forum\Subscriber;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Forum\Repository\MessageRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\Forum\TopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForumSubscriber implements EventSubscriberInterface
{
    private MessageRepository $messageRepository;
    private TopicRepository $topicRepository;
    private EntityManagerInterface $em;
    private TopicService $topicService;

    public function __construct(
        TopicRepository $topicRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $em,
        TopicService $topicService
    ) {
        $this->messageRepository = $messageRepository;
        $this->topicRepository = $topicRepository;
        $this->em = $em;
        $this->topicService = $topicService;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserBannedEvent::class => 'cleanUserContent',
            MessageCreatedEvent::class => 'onMessageCreated',
        ];
    }

    public function cleanUserContent(UserBannedEvent $event): void
    {
        $this->messageRepository->deleteForUser($event->getUser());
        $this->topicRepository->deleteForUser($event->getUser());
    }

    public function onMessageCreated(MessageCreatedEvent $event): void
    {
        $message = $event->getMessage();
        if (true === $message->isSpam()) {
            return;
        }
        $topic = $message->getTopic();
        $topic->setLastMessage($message);
        $topic->setUpdatedAt(new \DateTimeImmutable());
        $this->topicService->readTopic($topic, $message->getAuthor());
        $this->em->flush();
    }
}
