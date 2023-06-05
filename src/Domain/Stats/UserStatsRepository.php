<?php

namespace App\Domain\Stats;

use App\Domain\Auth\User;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserStatsRepository extends AbstractRepository
{

    public function __construct(ManagerRegistry $registry){
        parent::__construct($registry, User::class);
    }

    public function getDailySignups(): array
    {
        return $this->aggregateSignup('yyyy-mm-dd', 'dd', 30);
    }

    public function getMonthlySignups(): array
    {
        return $this->aggregateSignup('yyyy-mm', 'mm', 24);
    }

    private function aggregateSignup(string $group, string $label, int $limit): array
    {
        return array_reverse($this->createQueryBuilder('u')
            ->select(
                "TO_CHAR(u.createdAt, '$label') as date",
                "TO_CHAR(u.createdAt, '$group') as fulldate",
                'COUNT(u.id) as amount'
            )
            ->groupBy('fulldate', 'date')
            ->orderBy('fulldate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

}
