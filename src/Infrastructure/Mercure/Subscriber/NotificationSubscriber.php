<?php

namespace App\Infrastructure\Mercure\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;
    private PublisherInterface $publisher;

    public function __construct(SerializerInterface $serializer, PublisherInterface $publisher)
    {
        $this->serializer = $serializer;
        $this->publisher = $publisher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NotificationCreatedEvent::class => ['publishNotification'],
        ];
    }

    public function publishNotification(NotificationCreatedEvent $event): void
    {
        $notification = $event->getNotification();
        $channel = $notification->getChannel();
        if (null === $channel && $notification->getUser() instanceof User) {
            $channel = 'user/'.$notification->getUser()->getId();
        }
        $update = new Update("/notifications/$channel", $this->serializer->serialize($notification, 'json', [
            'groups' => ['read:notification'],
            'iri' => false,
        ]));
        $this->publisher->__invoke($update);
    }
}
