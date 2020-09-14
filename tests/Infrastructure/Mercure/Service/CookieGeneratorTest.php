<?php

namespace App\Infrastructure\Mercure\Service;

use App\Domain\Auth\User;
use App\Domain\Notification\NotificationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGeneratorTest extends TestCase
{
    private function assertCookieIsSubscribedTo(Cookie $cookie, array $channels)
    {
        $parts = explode('.', $cookie->getValue());
        $data = json_decode(base64_decode($parts[1]), true);
        $this->assertEquals($channels, $data['mercure']['subscribe']);
    }

    public function testUserIsSubscribedToHisOwnChannel()
    {
        $user = (new User())->setId(10);
        $security = $this->getMockBuilder(NotificationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $security->expects($this->once())->method('getChannelsForUser')->willReturn(['user/10']);
        $service = new CookieGenerator('secret', $security);
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
        $service = new CookieGenerator('secret', $security);
        $cookie = $service->generate($user);
        $this->assertCookieIsSubscribedTo($cookie, [
            '/notifications/user/10',
            '/notifications/admin',
        ]);
    }
}
