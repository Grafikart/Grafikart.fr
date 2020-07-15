<?php

namespace App\Domain\Contact;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactRequest::class);
    }

    public function findLastRequestForIp(string $ip): ?ContactRequest
    {
        return $this->createQueryBuilder('req')
            ->where('req.ip = :ip')
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
