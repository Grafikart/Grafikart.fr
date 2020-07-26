<?php

namespace App\Tests\Http\Admin\Firewall;

use App\Http\Admin\Firewall\AdminRequestListener;
use App\Tests\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminRequestListenerTest extends KernelTestCase
{
    public function urlDataProvier()
    {
        yield ['/admin', '/admin/blog', true];
        yield ['admin', '/admin/blog', true];
        yield ['/admin', '/admin/', true];
        yield ['/admin', '/admin', true];
        yield ['/admin', '/admin-de-blog', false];
        yield ['/admin', '/azeadmin/blog', false];
        yield ['/admin', '/admin/blog', false, true];
    }

    /**
     * @dataProvider urlDataProvier
     */
    public function testRequestListener(string $prefix, string $uri, bool $expectException, bool $isGranted = false)
    {
        $event = $this->getMockBuilder(RequestEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->any())
            ->method('isMasterRequest')
            ->willReturn(true);
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())
            ->method('getRequestUri')
            ->willReturn($uri);
        $event->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);
        $authChecker = $this->getMockBuilder(AuthorizationCheckerInterface::class)->getMock();
        $authChecker->expects($this->any())
            ->method('isGranted')
            ->willReturn($isGranted);

        $listener = new AdminRequestListener($prefix, $authChecker);
        if (true === $expectException) {
            $this->expectException(AccessDeniedException::class);
        } else {
            $this->addToAssertionCount(1);
        }
        $listener->onRequest($event);
    }
}
