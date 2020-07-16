<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Auth\User;
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
            $tags = [$tag];
            if ($tag->getChildren()->count() > 0) {
                $tags = $tag->getChildren()->toArray();
            }
            $query
                ->join('t.tags', 'tag')
                ->where('tag IN (:tags)')
                ->setParameter('tags', $tags);
        }

        return $query->getQuery();
    }

    public function findAllBatched(): iterable
    {
        $limit = 0;
        $perPage = 1000;
        while (true) {
            $rows = $this->createQueryBuilder('t')
                ->setMaxResults($perPage)
                ->setFirstResult($limit)
                ->getQuery()
                ->getResult();
            if (empty($rows)) {
                break;
            }
            foreach ($rows as $row) {
                yield $row;
            }
            $limit += $perPage;
            $this->getEntityManager()->clear();
        }
    }

    public function countForUser(User $user): int
    {
        return $this->count(['author' => $user]);
    }

    public function deleteForUser(User $user): void
    {
        $this->createQueryBuilder('t')
            ->where('t.author = :user')
            ->setParameter('user', $user)
            ->delete()
        ->getQuery()
        ->execute();
    }
}
