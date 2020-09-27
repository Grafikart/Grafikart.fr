<?php

namespace App\Tests\Domain\Badge\Repository;

use App\Domain\Badge\Entity\BadgeUnlock;
use App\Domain\Badge\Repository\BadgeUnlockRepository;
use App\Tests\FixturesTrait;
use App\Tests\RepositoryTestCase;

class BadgeUnlockRepositoryTest extends RepositoryTestCase
{
    use FixturesTrait;

    /**
     * @var BadgeUnlockRepository|null
     */
    protected $repository = null;
    protected $repositoryClass = BadgeUnlockRepository::class;

    public function testHasUnlockedWithNoBadge()
    {
        ['user1' => $user] = $this->loadFixtures(['badges']);
        $this->assertFalse($this->repository->hasUnlocked($user, 'gamer'));
    }

    public function testHasUnlockedWithBadge()
    {
        ['badgegamer' => $badge, 'user1' => $user] = $this->loadFixtures(['badges']);
        $this->em->persist(new BadgeUnlock($user, $badge));
        $this->em->flush();
        $this->assertTrue($this->repository->hasUnlocked($user, 'gamer'));
    }

    public function testHasUnlockedWithBadgeAboveLimit()
    {
        ['badgecomment5' => $badge, 'user1' => $user] = $this->loadFixtures(['badges']);
        $this->em->persist(new BadgeUnlock($user, $badge));
        $this->em->flush();
        $this->assertFalse($this->repository->hasUnlocked($user, 'comments', 100));
    }

    public function testFindUnlockableBadges()
    {
        ['user1' => $user, 'badgecomment5' => $badge] = $this->loadFixtures(['badges']);
        $this->assertCount(2, $this->repository->findUnlockableBadges($user, 'comments', 12));
        $this->assertCount(0, $this->repository->findUnlockableBadges($user, 'comments', 1));
        $this->assertCount(1, $this->repository->findUnlockableBadges($user, 'comments', 6));
        $this->em->persist(new BadgeUnlock($user, $badge));
        $this->em->flush();
        $this->assertCount(1, $this->repository->findUnlockableBadges($user, 'comments', 12));
    }
}
