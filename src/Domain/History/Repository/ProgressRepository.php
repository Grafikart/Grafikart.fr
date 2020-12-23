<?php

namespace App\Domain\History\Repository;

use App\Core\Orm\AbstractRepository;
use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\History\Entity\Progress;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Progress>
 */
class ProgressRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Progress::class);
    }

    public function findOneByContent(User $user, Content $content): ?Progress
    {
        return $this->findOneBy([
            'content' => $content,
            'author'  => $user,
        ]);
    }

    /**
     * @param Content[] $contents
     *
     * @return Progress[]
     */
    public function findForContents(User $user, array $contents): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.content', 'c')
            ->addSelect('partial c.{id}')
            ->where('p.content IN (:ids)')
            ->setParameter('ids', array_map(fn(Content $c) => $c->getId(), $contents))
            ->getQuery()
            ->getResult();
    }

    public function findLastForUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.content', 'c')
            ->where('p.author = :user')
            ->andWhere('c INSTANCE OF :type')
            ->andWhere('p.progress < :progress')
            ->orderBy('p.updatedAt', 'DESC')
            ->setMaxResults(4)
            ->setParameters([
                'user'     => $user,
                'type'     => 'course',
                'progress' => Progress::TOTAL,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les ids lu parmis la liste passée en paramètre
     *
     * @param User $user
     * @param array $ids
     * @return int[]
     */
    public function findFinishedIdWithin(User $user, array $ids): array
    {
        return array_map(fn(Progress $p) => $p->getContent()->getId(), $this->createQueryBuilder('p')
            ->where('p.content IN (:ids)')
            ->andWhere('p.author = :user')
            ->andWhere('p.progress = :total')
            ->setParameters([
                'user'  => $user,
                'ids'   => $ids,
                'total' => Progress::TOTAL
            ])
            ->getQuery()
            ->getResult());
    }
}
