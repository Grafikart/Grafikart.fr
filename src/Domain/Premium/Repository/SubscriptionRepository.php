<?php

namespace App\Domain\Premium\Repository;

use App\Core\Orm\AbstractRepository;
use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Subscription;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Subscription>
 */
class SubscriptionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findCurrentForUser(User $user): ?Subscription
    {
        return $this->createQueryBuilder('sub')
            ->where('sub.user = :user')
            ->setParameters([
                'user' => $user,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
