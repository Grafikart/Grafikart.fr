<?php

namespace App\Tests\Domain\Auth;

use App\Domain\Auth\Authenticator;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

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
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        /* @var MockObject|UrlMatcherInterface $urlMatcher */
        $urlMatcher = $this->createMock(UrlMatcherInterface::class);
        $urlMatcher->expects($this->any())->method('match')->willReturn([]);
        $this->authenticator = new Authenticator(
            $this->userRepository,
            $this->urlGenerator,
            $this->eventDispatcher,
            $this->createMock(UrlMatcherInterface::class),
        );
    }

    public function testPassportIsCorrectlySetOnAuthentication(): void
    {
        $request = new Request([], ['email' => 'john1@doe.fr']);
        $request->setSession(new Session(new MockArraySessionStorage()));

        $user = new User();
        $this->userRepository
            ->expects($this->once())
            ->method('findForAuth')
            ->with('john1@doe.fr')
            ->willReturn(new User())
        ;

        $passport = $this->authenticator->authenticate($request);
        $this->assertEquals($passport->getUser(), $user);
        $this->assertTrue($passport->hasBadge(CsrfTokenBadge::class));
        $this->assertTrue($passport->hasBadge(PasswordCredentials::class));
    }
}
