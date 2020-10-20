<?php

namespace App\Domain\Course\Repository;

use App\Domain\Blog\Category;
use App\Domain\Course\Entity\Cursus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CursusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cursus::class);
    }

    /**
     * @return Cursus[]
     */
    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.online = true')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
