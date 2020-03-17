<?php

namespace App\Domain\Course\Repository;

use App\Core\Orm\IterableQuery;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class FormationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function findForTechnology(Technology $technology): iterable
    {
        return new IterableQuery($this->createQueryBuilder('f')
            ->where('f.online = true')
            ->leftJoin('f.technologyUsages', 'usage')
            ->where('usage.technology = :technology')
            ->setParameter('technology', $technology)
            ->orderBy('f.createdAt', 'ASC'));
    }

}
