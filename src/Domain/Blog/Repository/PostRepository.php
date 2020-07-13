<?php

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[]
     */
    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
