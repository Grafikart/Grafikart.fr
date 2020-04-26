<?php

namespace App\Domain\Profile\Repository;

use App\Domain\Auth\User;
use App\Domain\Profile\Entity\EmailVerification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmailVerificationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerification::class);
    }

    public function findLastForUser(User $user): ?EmailVerification
    {
        return $this->createQueryBuilder('v')
            ->select('v.createdAt')
            ->where('v.author = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

}
