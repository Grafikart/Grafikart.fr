<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Forum\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findTree(): array
    {
        $query = $this->createQueryBuilder('t')
            ->addSelect('p')
            ->leftJoin('t.parent', 'p')
            ->orderBy('t.position', 'ASC');
        return array_values(array_filter(
            $query->getQuery()->getResult(),
            fn(Tag $tag) => $tag->getParent() === null
        ));

    }

}
