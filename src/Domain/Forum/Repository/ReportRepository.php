<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Forum\Entity\Report;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Report>
 */
class ReportRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }
}
