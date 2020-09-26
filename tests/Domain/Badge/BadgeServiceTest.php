<?php

namespace App\Tests\Domain\Badge;

use App\Domain\Auth\User;
use App\Domain\Badge\BadgeService;
use App\Domain\Badge\Entity\Badge;
use App\Domain\Badge\Entity\BadgeUnlock;
use App\Domain\Badge\Event\BadgeUnlockEvent;
use App\Domain\Badge\Repository\BadgeUnlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class BadgeServiceTest extends TestCase
{
    /**
     * @var MockObject|EntityManagerInterface
     */
    private EntityManagerInterface $em;
    private BadgeService $service;
    /**
     * @var MockObject|EventDispatcherInterface
     */
    private EventDispatcherInterface $dispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->service = new BadgeService($this->em, $this->dispatcher);
    }

    public function testDoesNothingIfBadgeAlreadyUnlocked()
    {
        $badgeUnlockRepository = $this->createMock(BadgeUnlockRepository::class);
        $badgeUnlockRepository->expects($this->once())->method('hasUnlocked')->willReturn(true);
        $this->em->expects($this->once())->method('getRepository')->with(BadgeUnlock::class)->willReturn($badgeUnlockRepository);
        $this->assertNull($this->service->unlock(new User(), 'demo'));
    }

    public function testUnlockBadgeIfNotAlreadyOwned()
    {
        $badgeUnlockRepository = $this->createMock(BadgeUnlockRepository::class);
        $badgeUnlockRepository->expects($this->once())->method('hasUnlocked')->willReturn(false);
        $badgeUnlockRepository->expects($this->once())->method('findUnlockableBadges')->willReturn([
            new Badge('demo', 'demo', 'demo'),
            new Badge('demo', 'demo', 'demo'),
        ]);
        $this->em->expects($this->once())->method('getRepository')->with(BadgeUnlock::class)->willReturn($badgeUnlockRepository);
        $this->em->expects($this->once())->method('flush');
        $this->em->expects($this->exactly(2))->method('persist')->with($this->isInstanceOf(BadgeUnlock::class));
        $this->dispatcher->expects($this->exactly(2))->method('dispatch')->with($this->isInstanceOf(BadgeUnlockEvent::class));
        $unlocks = $this->service->unlock(new User(), 'demo');
        $this->assertCount(2, $unlocks);
    }
}
