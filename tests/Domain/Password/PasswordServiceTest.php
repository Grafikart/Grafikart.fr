<?php

namespace App\Tests\Domain\Password;

use App\Domain\Auth\UserRepository;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Repository\PasswordResetTokenRepository;
use App\Domain\Password\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordServiceTest extends TestCase
{

    private \App\Domain\Password\PasswordService $service;

    public function setUp(): void
    {
        $userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $tokenRepository = $this->getMockBuilder(PasswordResetTokenRepository::class)->disableOriginalConstructor()->getMock();
        $generator = $this->getMockBuilder(TokenGeneratorService::class)->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();

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
        $this->assertTrue($this->service->isExpired((new PasswordResetToken())->setCreatedAt(new \DateTime('-40 minutes'))));
        $this->assertFalse($this->service->isExpired((new PasswordResetToken())->setCreatedAt(new \DateTime('-10 minutes'))));
    }

}
