<?php

namespace App\Domain\Revision;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Revision|null find($id, $lockMode = null, $lockVersion = null)
 * @method Revision|null findOneBy(array $criteria, array $orderBy = null)
 * @method Revision[]    findAll()
 * @method Revision[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevisionRepository extends ServiceEntityRepository
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
                'target' => $content
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

}
