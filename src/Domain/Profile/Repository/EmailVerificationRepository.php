<?php

namespace App\Domain\Profile\Repository;

use App\Domain\Auth\User;
use App\Domain\Profile\Entity\EmailVerification;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Orm\CleanableRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<EmailVerification>
 */
class EmailVerificationRepository extends AbstractRepository implements CleanableRepositoryInterface
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

    /**
     * Supprime les anciennes demande de verification d'email.
     */
    public function clean(): int
    {
        return $this->createQueryBuilder('v')
            ->where('v.createdAt < :date')
            ->setParameter('date', new \DateTime('-1 month'))
            ->delete(EmailVerification::class, 'v')
            ->getQuery()
            ->execute();
    }
}
