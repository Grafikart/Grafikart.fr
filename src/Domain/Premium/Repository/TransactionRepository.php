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
        return $this->aggregateRevenus('yyy-mm', 'mm', 24);
    }

    public function getDailyRevenues(): array
    {
        return $this->aggregateRevenus('yyy-mm-dd', 'dd', 30);
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
}
