<?php

namespace App\Domain\Profile\Repository;

use App\Core\Orm\AbstractRepository;
use App\Domain\Auth\User;
use App\Domain\Profile\Entity\EmailVerification;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<EmailVerification>
 */
class EmailVerificationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerification::class);
    }

    public function findLastForUser(User $user): ?EmailVerification
    {
        return $this->createQueryBuilder('v')
            ->where('v.author = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
