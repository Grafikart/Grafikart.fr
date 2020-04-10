<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class TopicRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function queryAllForTag(?Tag $tag): Query
    {
        $query = $this->createQueryBuilder('t')
            ->setMaxResults(20)
            ->orderBy('t.createdAt', 'DESC');
        if ($tag) {
            $query
                ->join('t.tags', 'tag')
                ->where('tag = :tag')
                ->setParameter('tag', $tag);
        }
        return $query->getQuery();
    }

}
