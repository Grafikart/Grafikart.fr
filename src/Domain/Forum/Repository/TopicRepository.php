<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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

    /**
     * Récupère la liste des utilisateurs ayant participé au forum mais n'ayant pas déjà été notifié.
     *
     * @return User[]
     */
    public function findUsersToNotify(Message $message): array
    {
        $topic = $message->getTopic();
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(User::class, 'u');
        $selectClause = $rsm->generateSelectClause(['u' => 'u']);

        // On trouve les utilisateurs qui ont posté des messages sur le sujet en question
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<SQL
                SELECT $selectClause
                FROM forum_message m
                LEFT JOIN forum_read_time rt on m.topic_id = rt.topic_id AND m.author_id = rt.owner_id
                LEFT JOIN "user" u on u.id = m.author_id
                WHERE
                      m.topic_id = :topic AND
                      (rt.notified IS false OR rt.notified IS NULL) AND
                      m.author_id != :user
                GROUP BY u.id
            SQL, $rsm);
        $query->setParameter('topic', $topic->getId());
        $query->setParameter('user', $message->getAuthor()->getId());
        $users = $query->getResult();

        // Si l'auteur du message est l'auteur du topic on s'arrète ici
        if ($message->getAuthor()->getId() === $message->getTopic()->getAuthor()->getId()) {
            return array_unique($users, SORT_REGULAR);
        }

        // On récupère l'auteur du sujet si il n'a pas déjà été notifié
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<SQL
                SELECT $selectClause
                FROM forum_topic t
                LEFT JOIN forum_read_time rt on t.id = rt.topic_id AND t.author_id = rt.owner_id
                LEFT JOIN "user" u on u.id = t.author_id
                WHERE t.id = :topic AND (rt.notified IS false OR rt.notified IS NULL)
            SQL, $rsm);
        $query->setParameter('topic', $topic->getId());
        $users = array_merge($users, $query->getResult());

        return array_unique($users, SORT_REGULAR);
    }
}
