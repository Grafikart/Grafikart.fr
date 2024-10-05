<?php

namespace App\Domain\Premium\Repository;

use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Transaction;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Transaction>
 */
class TransactionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @return Transaction[]
     */
    public function findFor(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.author = :user')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyRevenues(): array
    {
        return $this->aggregateRevenus('yyyy-mm', 'mm', 24);
    }

    public function getDailyRevenues(): array
    {
        return $this->aggregateRevenus('yyyy-mm-dd', 'dd', 30);
    }

    private function aggregateRevenus(string $group, string $label, int $limit): array
    {
        return array_reverse($this->createQueryBuilder('t')
            ->select(
                "TO_CHAR(t.createdAt, '$label') as date",
                "TO_CHAR(t.createdAt, '$group') as fulldate",
                'ROUND(SUM(t.price - t.tax - t.fee)) as amount'
            )
            ->groupBy('fulldate', 'date')
            ->where('t.refunded = false')
            ->orderBy('fulldate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    public function getMonthlyReport(int $year): array
    {
        return $this->createQueryBuilder('t')
            ->select(
                't.method as method',
                'EXTRACT(MONTH FROM t.createdAt) as month',
                'ROUND(SUM(t.price) * 100) / 100 as price',
                'ROUND(SUM(t.tax) * 100) / 100 as tax',
                'ROUND(SUM(t.fee) * 100) / 100 as fee',
            )
            ->groupBy('month', 't.method')
            ->where('t.refunded = false')
            ->andWhere('EXTRACT(YEAR FROM t.createdAt) = :year')
            ->setParameter('year', $year)
            ->orderBy('month', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getFiscalReport(int $year): array
    {
        $lastYear = $year - 1;
        return $this->createQueryBuilder('t')
            ->select(
                't.method as method',
                'EXTRACT(MONTH FROM t.createdAt) as month',
                'ROUND(SUM(t.price) * 100) as price',
                'ROUND(SUM(t.tax) * 100) as tax',
                'ROUND(SUM(t.fee) * 100) as fee',
            )
            ->groupBy('month', 't.method')
            ->where('t.refunded = false')
            ->andWhere('t.createdAt >= :start')
            ->andWhere('t.createdAt < :end')
            ->setParameter('start', "$lastYear-08-01")
            ->setParameter('end', "$year-08-01")
            ->orderBy('month', 'DESC')
            ->orderBy('t.method', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
