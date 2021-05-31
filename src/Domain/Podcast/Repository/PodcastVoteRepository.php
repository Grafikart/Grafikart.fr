<?php

namespace App\Domain\Podcast\Repository;

use App\Domain\Podcast\Entity\PodcastVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PodcastVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method PodcastVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method PodcastVote[]    findAll()
 * @method PodcastVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PodcastVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PodcastVote::class);
    }

    // /**
    //  * @return PodcastVote[] Returns an array of PodcastVote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PodcastVote
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
