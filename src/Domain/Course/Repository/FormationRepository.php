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

    /**
     * @return Formation[]
     */
    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.online = true')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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

    public function findForTechnologyPerLevel(Technology $technology): iterable
    {
        $technologies = $this->createQueryBuilder('f')
            ->where('f.online = true')
            ->leftJoin('f.technologyUsages', 'usage')
            ->where('usage.technology = :technology')
            ->setParameter('technology', $technology)
            ->orderBy('f.level', 'ASC')
            ->orderBy('f.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
        return collect($technologies)->groupBy(fn(Formation $t) => $t->getLevel())->toArray();
    }

}
