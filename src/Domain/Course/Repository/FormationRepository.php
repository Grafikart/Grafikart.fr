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
     * @return Formation[]
     */
    public function findAll(): array
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
            ->leftJoin('f.technologyUsages', 'usage')
            ->where('f.online = true')
            ->andWhere('usage.technology = :technology')
            ->andWhere('usage.secondary = false')
            ->setParameter('technology', $technology)
            ->orderBy('f.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
