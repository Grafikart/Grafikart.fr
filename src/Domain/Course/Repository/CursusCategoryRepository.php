<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\CursusCategory;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<CursusCategory>
 */
class CursusCategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CursusCategory::class);
    }
}
