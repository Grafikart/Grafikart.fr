<?php

namespace App\Infrastructure\Mercure\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Badge\Event\BadgeUnlockEvent;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use App\Domain\Notification\Event\NotificationReadEvent;
use App\Infrastructure\Queue\EnqueueMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class MercureSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;
    private PublisherInterface $publisher;

    private EnqueueMethod $enqueue;

    public function __construct(SerializerInterface $serializer, PublisherInterface $publisher, EnqueueMethod $enqueue)
    {
        $this->serializer = $serializer;
        $this->publisher = $publisher;
        $this->enqueue = $enqueue;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NotificationCreatedEvent::class => 'publishNotification',
            BadgeUnlockEvent::class => 'publishBadgeUnlock',
            NotificationReadEvent::class => 'onNotificationRead',
        ];
    }

    public function publishNotification(NotificationCreatedEvent $event): void
    {
        $notification = $event->getNotification();
        $channel = $notification->getChannel();
        if ('public' === $channel && $notification->getUser() instanceof User) {
            $channel = 'user/'.$notification->getUser()->getId();
        }
        $update = new Update("/notifications/$channel", $this->serializer->serialize([
            'type' => 'notification',
            'data' => $notification,
        ], 'json', [
            'groups' => ['read:notification'],
            'iri' => false,
        ]), true);
        $this->enqueue->enqueue(PublisherInterface::class, '__invoke', [$update]);
    }

    public function publishBadgeUnlock(BadgeUnlockEvent $event): void
    {
        $badge = $event->getBadge();
        $user = $event->getUser();
        $update = new Update("/notifications/user/{$user->getId()}", $this->serializer->serialize([
            'type' => 'badge',
            'data' => $badge,
        ], 'json'), true);
        $this->enqueue->enqueue(PublisherInterface::class, '__invoke', [$update], new \DateTimeImmutable('+ 2 seconds'));
    }

    public function onNotificationRead(NotificationReadEvent $event): void
    {
        $user = $event->getUser();
        $update = new Update(
            "/notifications/user/{$user->getId()}",
            '{"type": "markAsRead"}',
            true
        );
        $this->enqueue->enqueue(PublisherInterface::class, '__invoke', [$update]);
    }
}
