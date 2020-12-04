<?php

namespace App\Domain\Course\Repository;

use App\Core\Orm\AbstractRepository;
use App\Core\Orm\IterableQueryBuilder;
use App\Domain\Blog\Category;
use App\Domain\Course\Entity\Cursus;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Cursus>
 */
class CursusRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cursus::class);
    }

    /**
     * @return IterableQueryBuilder<Cursus>
     */
    public function findRecent(int $limit): IterableQueryBuilder
    {
        return $this->createIterableQuery('c')
            ->where('c.online = true')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit);
    }

    public function findByCategory(): array
    {
        $cursus = $this->createQueryBuilder('c')
            ->join('c.category', 'category')
            ->select('c', 'category')
            ->orderBy('category.position', 'ASC')
            ->where('c.online = true')
            ->getQuery()
            ->getResult();

        return collect($cursus)->groupBy(function (Cursus $c) {
            /** @var Category $category */
            $category = $c->getCategory();

            return $category->getName();
        })->toArray();
    }
}
