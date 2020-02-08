<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * TODO : Mettre en place une pagination
     * @return array<Course>
     */
    public function paginateAll(): array
    {
        return $this->createQueryBuilder('c')
            ->setMaxResults(16)
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
