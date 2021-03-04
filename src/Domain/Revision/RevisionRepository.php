<?php

namespace App\Domain\Revision;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\ORM\QueryBuilder;
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
            ->where('r.status = :status')
            ->setMaxResults(10)
            ->setParameter('status', Revision::PENDING)
            ->getQuery()
            ->getResult();
    }

    public function findFor(User $user, Content $content): ?Revision
    {
        return $this->createQueryBuilder('r')
            ->where('r.author = :author')
            ->andWhere('r.target = :target')
            ->andWhere('r.status = :status')
            ->setParameters([
                'author' => $user,
                'target' => $content,
                'status' => Revision::PENDING,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Revision[]
     */
    public function findPendingFor(User $user): array
    {
        return $this->queryAllForUser($user)
            ->andWhere('r.status = :status')
            ->setParameter('status', Revision::PENDING)
            ->getQuery()
            ->getResult();
    }

    public function queryAllForUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->addSelect('c')
            ->leftJoin('r.target', 'c')
            ->where('r.author = :user')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(10)
            ->setParameter('user', $user);
    }
}
