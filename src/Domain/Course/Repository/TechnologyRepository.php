<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\Technology;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TechnologyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    /**
     * Trouve des technologies par rapport à son nom (non sensible à la casse).
     *
     * @param string[] $names
     *
     * @return Technology[]
     */
    public function findByNames(array $names): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) IN (:name)')
            ->setParameter('name', array_map(fn (string $name) => strtolower($name), $names))
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une technologie par rapport à son nom (non sensible à la casse).
     *
     * @param string $name
     * @return Technology|null
     */
    public function findByName(string $name): ?Technology
    {
        return $this->createQueryBuilder('t')
            ->where('LOWER(t.name) = :technology')
            ->setMaxResults(1)
            ->setParameter('technology', strtolower($name))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
