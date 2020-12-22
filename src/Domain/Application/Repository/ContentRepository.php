<?php

namespace App\Domain\Application\Repository;

use App\Core\Orm\AbstractRepository;
use App\Domain\Application\Entity\Content;
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
     * @return Content[]
     */
    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
