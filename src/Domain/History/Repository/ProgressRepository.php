<?php

namespace App\Domain\History\Repository;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\History\Entity\Progress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Progress|null find($id, $lockMode = null, $lockVersion = null)
 * @method Progress|null findOneBy(array $criteria, array $orderBy = null)
 * @method Progress[]    findAll()
 * @method Progress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Progress::class);
    }

    public function findOneByContent(User $user, Content $content): ?Progress
    {
        return $this->findOneBy([
            'content' => $content,
            'author' => $user,
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
            ->setParameter('ids', array_map(fn (Content $c) => $c->getId(), $contents))
            ->getQuery()
            ->getResult();
    }
}
