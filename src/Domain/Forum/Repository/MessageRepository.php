<?php

namespace App\Domain\Forum\Repository;

use App\Core\Orm\AbstractRepository;
use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Infrastructure\Spam\SpammableRepositoryTrait;
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
}
