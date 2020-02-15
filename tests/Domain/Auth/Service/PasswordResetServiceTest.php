<?php

namespace App\Tests\Domain\Auth\Service;

use App\Domain\Auth\Entity\PasswordResetToken;
use App\Domain\Auth\Repository\PasswordResetTokenRepository;
use App\Domain\Auth\Service\PasswordResetService;
use App\Domain\Auth\Service\TokenGeneratorService;
use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetServiceTest extends TestCase
{

    private PasswordResetService $service;

    public function setUp(): void
    {
        $userRepository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $tokenRepository = $this->getMockBuilder(PasswordResetTokenRepository::class)->disableOriginalConstructor()->getMock();
        $generator = $this->getMockBuilder(TokenGeneratorService::class)->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $encoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();

        $this->service = new PasswordResetService(
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
