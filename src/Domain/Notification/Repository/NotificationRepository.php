<?php

namespace App\Domain\Notification\Repository;

use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param User $user
     * @return Notification[]
     */
    public function findRecentForUser(User $user): array
    {
        return array_map(fn ($n) => (clone $n)->setUser($user), $this->createQueryBuilder('notif')
            ->orderBy('notif.createdAt', 'DESC')
            ->setMaxResults(10)
            ->where('notif.createdAt < NOW()')
            ->andWhere('notif.user = :user OR notif.user IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult());
    }

    /**
     * Persiste une nouvelle notification ou met à jour une notification précédente
     */
    public function persistOrUpdate(Notification $notification): Notification
    {
        if ($notification->getUser() === null) {
            $this->getEntityManager()->persist($notification);
            return $notification;
        }
        $oldNotification = $this->findOneBy([
            'user' => $notification->getUser(),
            'target' => $notification->getTarget()
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

}
