<?php

namespace App\Domain\Live;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Live|null find($id, $lockMode = null, $lockVersion = null)
 * @method Live|null findOneBy(array $criteria, array $orderBy = null)
 * @method Live[]    findAll()
 * @method Live[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Live::class);
    }

    public function queryAll(): Query
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * @return array<int,int>
     */
    public function findYears(): array
    {
        return array_map(fn ($row) => (int)$row['year'], $this->createQueryBuilder('l')
            ->select('EXTRACT(year from l.createdAt) as year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getArrayResult());
    }

    /**
     * @return array<Live>
     */
    public function findForYear(int $year): array
    {
        $start = new \DateTimeImmutable("01-01-{$year}");
        $end = $start->add(new \DateInterval('P1Y'));
        return $this->createQueryBuilder('l')
            ->where('l.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la date du dernier live publié sur le site
     */
    public function lastCreationDate(): ?\DateTimeInterface
    {
        $date = $this->createQueryBuilder('l')
            ->select('MAX(l.createdAt)')
            ->getQuery()
            ->getSingleScalarResult();
        return $date ? new \DateTime($date) : null;
    }
}
