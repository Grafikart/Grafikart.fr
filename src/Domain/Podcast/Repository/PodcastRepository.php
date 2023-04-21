<?php

namespace App\Domain\Podcast\Repository;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Orm\IterableQueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Podcast>
 */
class PodcastRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Podcast::class);
    }

    /**
     * @return IterableQueryBuilder<Podcast>
     */
    public function findRecent(int $limit, bool $published = false): IterableQueryBuilder
    {
        $qb = $this->createIterableQuery('p')
            ->select('p')
            ->where('p.createdAt < NOW()');

        if ($published) {
            $qb->andWhere('(p.youtube IS NOT NULL OR p.mp3 IS NOT NULL)');
        }

        return $qb->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit);
    }

    /**
     * Renvoie les podcasts à venir.
     *
     * @return Podcast[]
     */
    public function findFuture(): array
    {
        return $this->createQueryBuilder('p')
            ->select('partial p.{id, scheduledAt, title, createdAt}', 'partial a.{id, username, avatarName}')
            ->join('p.author', 'a')
            ->where('p.scheduledAt > NOW()')
            ->orderBy('p.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findRelative(Podcast $podcast): array
    {
        $scheduledAt = $podcast->getScheduledAt() ?: new \DateTime();
        $date = (new \DateTime())->setTimestamp($scheduledAt->getTimestamp() + 300 * 24 * 60 * 60);

        return $this->queryPast()
            ->andWhere('p.scheduledAt < :date')
            ->setParameter('date', $date)
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }

    /**
     * Requête les podcasts déjà diffusés.
     */
    public function queryPast(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.scheduledAt IS NOT NULL')
            ->andWhere('p.scheduledAt < NOW()')
            ->andWhere('(p.youtube IS NOT NULL OR p.mp3 IS NOT NULL)')
            ->orderBy('p.scheduledAt', 'DESC');
    }

    /**
     * Requête les podcasts déjà diffusés.
     */
    public function querySuggestions(?string $orderKey): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'partial a.{id, username}')
            ->where('p.scheduledAt IS NULL')
            ->join('p.author', 'a')
            ->orderBy('popular' === $orderKey ? 'p.votesCount' : 'p.createdAt', 'DESC');
    }

    /**
     * Récupère et injecte les intervenants dans les podcasts.
     *
     * @param Podcast[] $podcasts
     */
    public function hydrateIntervenants(array $podcasts): array
    {
        foreach ($podcasts as $podcast) {
            $podcast->setIntervenants([]);
        }
        $em = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addEntityResult(Podcast::class, 'p');
        $rsm->addJoinedEntityFromClassMetadata(User::class, 'u', 'p', 'intervenants', ['id' => 'user_id']);
        $rsm->addFieldResult('p', 'podcast_id', 'id');
        $rsm->addFieldResult('u', 'user_id', 'id');
        $rsm->addFieldResult('u', 'username', 'username');
        $rsm->addFieldResult('u', 'avatar_name', 'avatarName');
        $query = $em->createNativeQuery(<<<SQL
            SELECT pu.podcast_id as podcast_id, pu.user_id, u.username, u.avatar_name, u.username
            FROM podcast_user pu
            LEFT JOIN "user" u ON pu.user_id = u.id
            WHERE pu.podcast_id IN (?)
        SQL, $rsm);
        $query->setParameter(1, array_map(fn (Podcast $p) => $p->getId(), $podcasts));

        return $query->getResult();
    }

    public function countRecentFromUser(User $user): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.author = :author')
            ->andWhere('p.createdAt > :date')
            ->setParameters([
                'author' => $user->getId(),
                'date' => new \DateTime('-1 month'),
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }
}
