<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Forum\Entity\Tag;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Tag>
 */
class TagRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @return Tag[]
     */
    public function findTree(): array
    {
        $query = $this->createQueryBuilder('t')
            ->andWhere('t.visible = true')
            ->orderBy('t.position', 'ASC');

        return array_values(array_filter(
            $query->getQuery()->getResult(),
            fn (Tag $tag) => null === $tag->getParent()
        ));
    }

    /**
     * @return Tag[]
     */
    public function findAllOrdered(): array
    {
        $parents = $this->findTree();
        $tags = [];
        foreach ($parents as $parent) {
            $tags[] = $parent;
            foreach ($parent->getChildren() as $child) {
                $tags[] = $child;
            }
        }

        return $tags;
    }
}
