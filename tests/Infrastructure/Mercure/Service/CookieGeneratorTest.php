<?php

namespace App\Infrastructure\Mercure\Service;

use App\Domain\Auth\User;
use App\Domain\Notification\NotificationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGeneratorTest extends TestCase
{
    const SECRET = 'random_secret_string_test_security';

    private function assertCookieIsSubscribedTo(Cookie $cookie, array $channels)
    {
        $parts = explode('.', $cookie->getValue());
        $data = json_decode(base64_decode($parts[1]), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals($channels, $data['mercure']['subscribe']);
    }

    public function testUserIsSubscribedToHisOwnChannel()
    {
        $user = (new User())->setId(10);
        $security = $this->getMockBuilder(NotificationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $security->expects($this->once())->method('getChannelsForUser')->willReturn(['user/10']);
        $service = new CookieGenerator(self::SECRET, $security);
        $cookie = $service->generate($user);
        $this->assertCookieIsSubscribedTo($cookie, ['/notifications/user/10']);
    }

    public function testAdminIsSubscribedToAdminChannel()
    {
        $user = (new User())->setId(10);
        $security = $this->getMockBuilder(NotificationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $security->expects($this->once())->method('getChannelsForUser')->willReturn(['user/10', 'admin']);
        $service = new CookieGenerator(self::SECRET, $security);
        $cookie = $service->generate($user);
        $this->assertCookieIsSubscribedTo($cookie, [
            '/notifications/user/10',
            '/notifications/admin',
        ]);
    }
}
