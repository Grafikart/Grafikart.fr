<?php

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Category;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Category>
 */
class CategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findWithCount(): array
    {
        $data = $this->createQueryBuilder('c')
            ->join('c.posts', 'p')
            ->where('p.online = true')
            ->groupBy('c.id')
            ->select('c', 'COUNT(c.id) as count')
            ->getQuery()
            ->getResult();

        return array_map(function (array $d) {
            $d[0]->setPostsCount((int) $d['count']);

            return $d[0];
        }, $data);
    }
}
