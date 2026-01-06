<?php

namespace App\Domain\Stats;

use App\Domain\Auth\User;
use App\Http\Admin\Data\Chart\DailyData;
use App\Http\Admin\Data\Chart\MonthlyData;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserStatsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return DailyData[]
     */
    public function getDailySignups(): array
    {
        /** @var array{date: string, fulldate: string, amount: int}[] $data */
        $data = $this->aggregateSignup('yyyy-mm-dd', 'dd', 30);
        return array_map(fn (array $datum) => new DailyData(date: $datum['fulldate'], value: $datum['amount']), $data);
    }

    /**
     * @return MonthlyData[]
     */
    public function getMonthlySignups(): array
    {
        /** @var array{date: string, fulldate: string, amount: int}[] $data */
        $data = $this->aggregateSignup('yyyy-mm', 'mm', 24);
        return array_map(fn (array $datum) => new MonthlyData(month: intval($datum['date']), year: intval(explode('-', $datum['fulldate'])[0]), value: $datum['amount']), $data);
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
