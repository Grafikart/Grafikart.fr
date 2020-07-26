<?php

namespace App\Tests\Domain\Auth\Service;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Auth\Service\UserBanService;
use App\Domain\Auth\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserBanServiceTest extends TestCase
{
    /**
     * @var MockObject|EventDispatcherInterface
     */
    private \PHPUnit\Framework\MockObject\MockObject $dispatcher;
    private UserBanService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->service = new UserBanService($this->dispatcher);
    }

    public function testSetBanCorrectly(): void
    {
        $user = new User();
        $this->service->ban($user);
        $this->assertTrue($user->isBanned());
    }

    public function testEmitBanEvent(): void
    {
        $this->dispatcher->expects($this->once())->method('dispatch')->with(
            $this->isInstanceOf(UserBannedEvent::class),
        );
        $this->service->ban(new User());
    }
}
