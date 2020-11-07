<?php

namespace App\Domain\Premium\Repository;

use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
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
                'SUM(t.price - t.tax) * 0.966 - COUNT(t.price) * 0.25 as amount'
            )
            ->groupBy('fulldate', 'date')
            ->where('t.refunded = false')
            ->orderBy('fulldate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }
}
