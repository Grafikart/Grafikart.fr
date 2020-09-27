<?php

namespace App\Domain\Badge\Repository;

use App\Domain\Auth\User;
use App\Domain\Badge\Entity\Badge;
use App\Domain\Badge\Entity\BadgeUnlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BadgeUnlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method BadgeUnlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method BadgeUnlock[]    findAll()
 * @method BadgeUnlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeUnlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BadgeUnlock::class);
    }

    public function hasUnlocked(User $user, string $action, int $count = 0): bool
    {
        return 0 === (int) $this->getEntityManager()->createQuery(<<<DQL
          SELECT COUNT(b.id) as c FROM App\Domain\Badge\Entity\Badge b
          WHERE NOT EXISTS (
            SELECT bu.id FROM App\Domain\Badge\Entity\BadgeUnlock bu WHERE bu.badge = b.id AND bu.owner = :user
          )
          AND b.action = :action AND b.actionCount <= :count
        DQL)
            ->setParameters(compact('user', 'action', 'count'))
            ->getSingleScalarResult();
    }

    /**
     * @return Badge[]
     */
    public function findUnlockableBadges(User $user, string $action, int $count = 0): array
    {
        return $this->getEntityManager()->createQuery(<<<DQL
          SELECT b FROM App\Domain\Badge\Entity\Badge b
          WHERE NOT EXISTS (
            SELECT bu.id FROM App\Domain\Badge\Entity\BadgeUnlock bu WHERE bu.badge = b.id AND bu.owner = :user
          )
          AND b.action = :action AND b.actionCount <= :count
        DQL)
            ->setParameters(compact('user', 'action', 'count'))
            ->getResult();
    }
}
