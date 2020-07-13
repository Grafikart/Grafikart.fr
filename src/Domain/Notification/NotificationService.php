<?php

namespace App\Domain\Notification;

use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use App\Domain\Notification\Repository\NotificationRepository;
use App\Http\Encoder\PathEncoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationService
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Envoie une notification sur un canal particulier.
     */
    public function notifyChannel(string $channel, string $message, object $entity): Notification
    {
        /** @var string $url */
        $url = $this->serializer->serialize($entity, PathEncoder::FORMAT);
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($this->getHashForEntity($entity))
            ->setCreatedAt(new \DateTime())
            ->setChannel($channel);
        $this->em->persist($notification);
        $this->em->flush();
        $this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    /**
     * Envoie une notification à un utilisateur.
     */
    public function notifyUser(User $user, string $message, object $entity): Notification
    {
        /** @var string $url */
        $url = $this->serializer->serialize($entity, PathEncoder::FORMAT);
        /** @var NotificationRepository $repository */
        $repository = $this->em->getRepository(Notification::class);
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($this->getHashForEntity($entity))
            ->setCreatedAt(new \DateTime())
            ->setUser($user);
        $repository->persistOrUpdate($notification);
        $this->em->flush();
        $this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    /**
     * @return Notification[]
     */
    public function forUser(User $user): array
    {
        /** @var NotificationRepository $repository */
        $repository = $this->em->getRepository(Notification::class);

        return $repository->findRecentForUser($user);
    }

    /**
     * Extrait un hash pour une notification className::id.
     */
    private function getHashForEntity(object $entity): string
    {
        $hash = get_class($entity);
        if (method_exists($entity, 'getId')) {
            $hash .= '::'.(string) $entity->getId();
        }

        return $hash;
    }
}
