<?php

namespace App\Domain\Revision;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Revision>
 */
class RevisionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Revision::class);
    }

    public function findLatest(): array
    {
        return $this->createQueryBuilder('r')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findFor(User $user, Content $content): ?Revision
    {
        return $this->createQueryBuilder('r')
            ->where('r.author = :author')
            ->andWhere('r.target = :target')
            ->setParameters([
                'author' => $user,
                'target' => $content,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
