<?php

namespace App\Domain\Course\Repository;

use App\Domain\Course\Entity\Technology;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Technology>
 */
class TechnologyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    public function findByType(): array
    {
        $types = [
            'BackEnd' => ['php', 'laravel', 'symfony', 'wordpress', 'nodejs'],
            'FrontEnd' => ['html', 'css', 'javascript', 'react', 'vuejs', 'webpack'],
            'Outils' => ['unix', 'git'],
        ];
        $slugs = [];
        foreach ($types as $v) {
            $slugs = array_merge($slugs, $v);
        }
        $technologies = $this->findBy(['slug' => $slugs]);
        if (empty($technologies)) {
            return [];
        }
        $technologies = collect($technologies)->keyBy(fn (Technology $t) => $t->getSlug())->toArray();
        foreach ($types as $k => $v) {
            $types[$k] = collect($v)->map(fn (string $slug) => $technologies[$slug])->toArray();
        }

        return $types;
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

    /**
     * Trouve toutes les technologies qui commence par le mot.
     */
    public function searchByName(string $q): array
    {
        return $this->createQueryBuilder('t')
            ->where('LOWER(t.name) LIKE :q')
            ->setParameter('q', strtolower($q).'%')
            ->getQuery()
            ->getResult();
    }
}
