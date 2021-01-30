<?php

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Orm\IterableQueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Post>
 */
class PostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return IterableQueryBuilder<Post>
     */
    public function findRecent(int $limit): IterableQueryBuilder
    {
        return $this->createIterableQuery('p')
            ->select('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit);
    }

    public function queryAll(?Category $category = null): Query
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC');

        if ($category) {
            $query = $query
                ->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        return $query->getQuery();
    }
}
