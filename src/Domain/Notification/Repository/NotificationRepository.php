<?php

namespace App\Domain\Notification\Repository;

use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Orm\CleanableRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Notification>
 */
class NotificationRepository extends AbstractRepository implements CleanableRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param string[] $channels
     *
     * @return Notification[]
     */
    public function findRecentForUser(User $user, array $channels = ['public']): array
    {
        return array_map(fn ($n) => (clone $n)->setUser($user), $this->createQueryBuilder('notif')
            ->orderBy('notif.createdAt', 'DESC')
            ->setMaxResults(10)
            ->where('notif.user = :user')
            ->orWhere('notif.user IS NULL AND notif.channel IN (:channels)')
            ->setParameter('user', $user)
            ->setParameter('channels', $channels)
            ->getQuery()
            ->getResult());
    }

    /**
     * Persiste une nouvelle notification ou met à jour une notification précédente.
     */
    public function persistOrUpdate(Notification $notification): Notification
    {
        if (null === $notification->getUser()) {
            $this->getEntityManager()->persist($notification);

            return $notification;
        }
        $oldNotification = $this->findOneBy([
            'user' => $notification->getUser(),
            'target' => $notification->getTarget(),
        ]);
        if ($oldNotification) {
            $oldNotification->setCreatedAt($notification->getCreatedAt());
            $oldNotification->setMessage($notification->getMessage());

            return $oldNotification;
        } else {
            $this->getEntityManager()->persist($notification);

            return $notification;
        }
    }

    /**
     * Supprime les anciennes notifications.
     */
    public function clean(): int
    {
        return $this->createQueryBuilder('n')
            ->where('n.createdAt < :date')
            ->setParameter('date', new \DateTime('-3 month'))
            ->delete(Notification::class, 'n')
            ->getQuery()
            ->execute();
    }
}
