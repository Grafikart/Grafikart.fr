<?php

namespace App\Domain\Contact;

use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ContactRequest>
 */
class ContactRequestRepository extends AbstractRepository
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
