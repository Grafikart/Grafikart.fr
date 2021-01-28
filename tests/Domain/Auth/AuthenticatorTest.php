<?php

namespace App\Tests\Domain\Auth;

use App\Domain\Auth\Authenticator;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AuthenticatorTest extends TestCase
{
    /**
     * @var MockObject|UserRepository
     */
    private MockObject $userRepository;

    /**
     * @var MockObject|UrlGeneratorInterface
     */
    private MockObject $urlGenerator;

    /**
     * @var MockObject|CsrfTokenManagerInterface
     */
    private MockObject $csrfTokenManager;

    /**
     * @var MockObject|UserPasswordEncoderInterface
     */
    private MockObject $passwordEncoder;

    /**
     * @var MockObject|EventDispatcherInterface
     */
    private MockObject $eventDispatcher;

    private Authenticator $authenticator;

    protected function setUp(): void
    {
        parent::setUp();
        /* @var MockObject|EntityManagerInterface entityManager */
        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()->getMock();
        /* @var MockObject|UrlGeneratorInterface urlGenerator */
        $this->urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
        /* @var MockObject|CsrfTokenManagerInterface csrfTokenManager */
        $this->csrfTokenManager = $this->getMockBuilder(CsrfTokenManagerInterface::class)->getMock();
        $this->csrfTokenManager
            ->expects($this->any())
            ->method('isTokenValid')
            ->willReturn(true);

        /* @var MockObject|UserPasswordEncoderInterface passwordEncoder */
        $this->passwordEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        /* @var MockObject|EventDispatcherInterface eventDispatcher */
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        /* @var MockObject|UrlMatcherInterface $urlMatcher */
        $urlMatcher = $this->getMockBuilder(UrlMatcherInterface::class)->getMock();
        $urlMatcher->expects($this->any())->method('match')->willReturn([]);
        $this->authenticator = new Authenticator(
            $this->userRepository,
            $this->urlGenerator,
            $this->csrfTokenManager,
            $this->passwordEncoder,
            $this->eventDispatcher,
            $urlMatcher
        );
    }

    public function testGetUserReturnUser(): void
    {
        $provider = $this->getMockBuilder(UserProviderInterface::class)->getMock();

        $this->userRepository
            ->expects($this->once())
            ->method('findForAuth')
            ->with('john1@doe.fr')
            ->willReturn(new User())
        ;

        $this->authenticator->getUser(['email' => 'john1@doe.fr', 'csrf_token' => 'a'], $provider);
    }
}
