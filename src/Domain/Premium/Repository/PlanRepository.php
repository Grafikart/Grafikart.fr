<?php

namespace App\Domain\Premium\Repository;

use App\Core\Orm\AbstractRepository;
use App\Domain\Premium\Entity\Plan;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Plan>
 */
class PlanRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('plan')
            ->orderBy('plan.duration', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
