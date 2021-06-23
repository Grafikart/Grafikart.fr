<?php

namespace App\Domain\Podcast\Repository;

use App\Domain\Auth\User;
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

    public function podcastIdsForUser(?User $user): array
    {
        if (null === $user) {
            return [];
        }
        $results = $this->createQueryBuilder('pv')
            ->select('IDENTITY(pv.podcast) as id')
            ->where('pv.voter = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getArrayResult();

        return array_map(fn (array $r) => $r['id'], $results);
    }
}
