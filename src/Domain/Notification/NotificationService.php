<?php

namespace App\Domain\Notification;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use App\Domain\Notification\Event\NotificationReadEvent;
use App\Domain\Notification\Repository\NotificationRepository;
use App\Http\Encoder\PathEncoder;
use App\Http\Security\ChannelVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationService
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private EventDispatcherInterface $dispatcher;
    private Security $security;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Security $security
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
        $this->security = $security;
    }

    /**
     * Envoie une notification sur un canal particulier.
     */
    public function notifyChannel(string $channel, string $message, ?object $entity = null): Notification
    {
        /** @var string $url */
        $url = $entity ? $this->serializer->serialize($entity, PathEncoder::FORMAT) : null;
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($entity ? $this->getHashForEntity($entity) : null)
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

        // Si on notifie à propos d'un message du forum, la cible devient le topic
        if ($entity instanceof Message) {
            $entity = $entity->getTopic();
        }
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

        return $repository->findRecentForUser($user, $this->getChannelsForUser($user));
    }

    public function readAll(User $user): void
    {
        $user->setNotificationsReadAt(new \DateTimeImmutable());
        $this->em->flush();
        $this->dispatcher->dispatch(new NotificationReadEvent($user));
    }

    /**
     * Renvoie les salons auquel l'utilisateur peut s'abonner.
     *
     * @return string[]
     */
    public function getChannelsForUser(User $user): array
    {
        $channels = [
            'user/'.$user->getId(),
            'public',
        ];

        if ($this->security->isGranted(ChannelVoter::LISTEN_ADMIN)) {
            $channels[] = 'admin';
        }

        return $channels;
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
