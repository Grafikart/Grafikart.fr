<?php

namespace App\Domain\Badge\Repository;

use App\Core\Orm\AbstractRepository;
use App\Domain\Badge\Entity\Badge;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Badge>
 */
class BadgeRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }
}
