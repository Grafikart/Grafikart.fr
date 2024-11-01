<?php

namespace App\Tests\Domain\Password;

use App\Domain\Auth\UserRepository;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Repository\PasswordResetTokenRepository;
use App\Infrastructure\Security\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordServiceTest extends TestCase
{
    private \App\Domain\Password\PasswordService $service;

    public function setUp(): void
    {
        parent::setUp();
        $userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $tokenRepository = $this->getMockBuilder(PasswordResetTokenRepository::class)->disableOriginalConstructor()->getMock();
        $generator = $this->getMockBuilder(TokenGeneratorService::class)->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $encoder = $this->getMockBuilder(UserPasswordHasherInterface::class)->getMock();

        $this->service = new \App\Domain\Password\PasswordService(
            $userRepository,
            $tokenRepository,
            $generator,
            $em,
            $dispatcher,
            $encoder
        );
        parent::setUp();
    }

    public function testIsExpired(): void
    {
        $this->assertTrue($this->service->isExpired((new PasswordResetToken())->setCreatedAt(new \DateTimeImmutable('-40 minutes'))));
        $this->assertFalse($this->service->isExpired((new PasswordResetToken())->setCreatedAt(new \DateTimeImmutable('-10 minutes'))));
    }
}
