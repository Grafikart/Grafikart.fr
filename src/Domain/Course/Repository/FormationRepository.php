<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Orm\IterableQueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Formation>
 */
class FormationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * @return formation[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('f')
            ->where('f.online = true')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return IterableQueryBuilder<Formation>
     */
    public function findRecent(int $limit): IterableQueryBuilder
    {
        return $this->createIterableQuery('f')
            ->where('f.online = true')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults($limit);
    }

    public function findForTechnology(Technology $technology): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.online = true')
            ->leftJoin('f.technologyUsages', 'usage')
            ->where('usage.technology = :technology')
            ->setParameter('technology', $technology)
            ->orderBy('f.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
