<?php

namespace App\Domain\Forum\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Forum\Repository\MessageRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\Forum\TopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
            KernelEvents::VIEW => ['onMessageDeleted', EventPriorities::POST_WRITE],
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
        $topic->setMessageCount($topic->getMessageCount() + 1);
        $topic->setUpdatedAt(new \DateTimeImmutable());
        $this->topicService->readTopic($topic, $message->getAuthor());
        $this->em->flush();
    }

    public function onMessageDeleted(ViewEvent $event): void
    {
        $method = $event->getRequest()->getMethod();
        $message = $event->getRequest()->attributes->get('data');

        if (!$message instanceof Message || Request::METHOD_DELETE !== $method) {
            return;
        }

        $topic = $message->getTopic();
        $topic->setMessageCount($topic->getMessages()->count());
        $topic->setLastMessage($topic->getMessages()->last() ?: null);
        $this->em->flush();
    }
}
