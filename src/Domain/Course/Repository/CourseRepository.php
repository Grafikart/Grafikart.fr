<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function queryAll(): Query
    {
        return $this->createQueryBuilder('c')
            ->where('c.online = true')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * @return Course[]
     */
    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.online = true')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function queryForTechnology(Technology $technology): Query
    {
        return $this->createQueryBuilder('c')
            ->where('c.online = true')
            ->leftJoin('c.technologyUsages', 'usage')
            ->where('usage.technology = :technology')
            ->andwhere('c.formation IS NULL')
            ->setParameter('technology', $technology)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ;
    }
}
