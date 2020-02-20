<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\Course;
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

}
