<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\ReadTime;
use App\Domain\Forum\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReadTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadTime::class);
    }

    /**
     * Met à jour la date de dernière lecture pour le sujet en question.
     */
    public function updateReadTimeForTopic(Topic $topic, User $user): ReadTime
    {
        // On cherche si on a déjà une lecture enregistrée
        $lastReadTime = $this->createQueryBuilder('r')
            ->where('r.topic = :topic')
            ->andWhere('r.owner = :user')
            ->setParameters([
                'topic' => $topic,
                'user' => $user,
            ])
            ->getQuery()
            ->getOneOrNullResult();

        // Le sujet n'a pas été mis à jour depuis
        if ($lastReadTime && $lastReadTime->getReadAt() > $topic->getUpdatedAt()) {
            return $lastReadTime;
        }

        // Si on n'a pas de date de dernière lecture on en crée une
        if (null === $lastReadTime) {
            $lastReadTime = (new ReadTime())
                ->setTopic($topic)
                ->setOwner($user);
            $this->getEntityManager()->persist($lastReadTime);
        }

        // On met à jour la date de dernière lecture
        $lastReadTime->setReadAt(new \DateTime());

        return $lastReadTime;
    }

    /**
     * @param Topic[] $topics
     *
     * @return ReadTime[]
     */
    public function findReadByTopicsAndUser(array $topics, User $user): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.topic', 'topic')
            ->where('r.topic IN (:topics)')
            ->andWhere('r.owner = :user')
            ->andWhere('topic.updatedAt < r.readAt')
            ->setParameters([
                'topics' => $topics,
                'user' => $user,
            ])
            ->getQuery()
            ->getResult();
    }

    public function deleteAllForUser(User $user): void
    {
        $this->createQueryBuilder('r')
            ->where('r.owner = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }
}
