<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Orm\IterableQueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Course>
 */
class CourseRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where('c.online = true')
            ->orderBy('c.createdAt', 'DESC');
    }

    public function queryAllPremium(): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('c.premium = :premium OR c.createdAt > NOW()')
            ->setParameter('premium', true);
    }

    /**
     * @return IterableQueryBuilder<Course>
     */
    public function findRecent(int $limit): IterableQueryBuilder
    {
        return $this->createIterableQuery('c')
            ->where('c.online = true')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit);
    }

    public function queryForTechnology(Technology $technology): Query
    {
        $courseClass = Course::class;
        $usageClass = TechnologyUsage::class;

        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT c
            FROM  $courseClass c
            JOIN c.technologyUsages ct WITH ct.technology = :technology AND ct.secondary = false
            WHERE NOT EXISTS (
                SELECT t FROM $usageClass t WHERE t.content = c.formation AND t.technology = :technology
            )
            AND c.online = true
            ORDER BY c.createdAt DESC
        DQL
        )->setParameter('technology', $technology);
    }

    public function findTotalDuration(): int
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(c.duration)')
            ->where('c.online = true')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Course[]
     */
    public function findRandom(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('RANDOM()')
            ->where('c.online = true')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
