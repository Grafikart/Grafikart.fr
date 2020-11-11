<?php

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
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
