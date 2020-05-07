<?php

namespace App\Domain\Notification;

use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Repository\NotificationRepository;
use App\Http\Encoder\PathEncoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationService
{

    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private PublisherInterface $publisher;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        PublisherInterface $publisher
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->publisher = $publisher;
    }

    public function notifyChannel(string $channel, string $message, object $entity): Notification
    {
        /** @var string $url */
        $url =$this->serializer->serialize($entity, PathEncoder::FORMAT);
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setCreatedAt(new \DateTime())
            ->setChannel('content');
        $this->em->persist($notification);
        $this->em->flush();

        // On publie sur mercure
        $update = new Update(
            'http://grafikart.fr/notifications/' . $channel,
            (string)json_encode([
                'message' => $message,
                'url' => $url
            ])
        );
        $this->publisher->__invoke($update);

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

}
