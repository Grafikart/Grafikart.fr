<?php

namespace App\Domain\Forum\Repository;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Spam\SpammableRepositoryTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Message>
 */
class MessageRepository extends AbstractRepository
{
    use SpammableRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function deleteForUser(User $user): void
    {
        $this->createQueryBuilder('m')
            ->where('m.author = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }

    /**
     * Force l'hydratation des messages (pour éviter de faire n+1 requêtes).
     */
    public function hydrateMessages(Topic $topic): Topic
    {
        $messages = $this->createQueryBuilder('m')
            ->where('m.topic = :topic')
            ->join('m.author', 'u')
            ->select('m, partial u.{id, username, email, avatarName}')
            ->setParameter('topic', $topic)
            ->orderBy('m.accepted', 'DESC')
            ->addOrderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
        $topic->setMessages(new ArrayCollection($messages));

        return $topic;
    }
}
