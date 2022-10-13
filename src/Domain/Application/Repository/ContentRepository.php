<?php

namespace App\Domain\Application\Repository;

use App\Domain\Application\Entity\Content;
use App\Infrastructure\Orm\AbstractRepository;
use App\Infrastructure\Orm\IterableQueryBuilder;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Content>
 */
class ContentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    /**
     * @return IterableQueryBuilder<Content>
     */
    public function findLatest(int $limit = 5, bool $withPremium = true): IterableQueryBuilder
    {
        $queryBuilder = $this->createIterableQuery('c')
            ->orderBy('c.createdAt', 'DESC')
            ->where('c.online = TRUE')
            ->setMaxResults($limit);

        if (!$withPremium) {
            $date = new \DateTimeImmutable('+ 3 days');
            $queryBuilder = $queryBuilder
                ->andWhere('c.createdAt < :published_at')
                ->setParameter('published_at', $date, Types::DATETIME_IMMUTABLE);
        }

        return $queryBuilder;
    }

    /**
     * @return IterableQueryBuilder<Content>
     */
    public function findLatestPublished(int $limit = 5): IterableQueryBuilder
    {
        return $this->findLatest($limit)->andWhere('c.createdAt < NOW()');
    }
}
