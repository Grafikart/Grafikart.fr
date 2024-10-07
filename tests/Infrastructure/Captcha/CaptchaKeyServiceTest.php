<?php

namespace App\Tests\Infrastructure\Captcha;

use App\Infrastructure\Captcha\CaptchaKeyService;
use App\Infrastructure\Captcha\TooManyTryException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CaptchaKeyServiceTest extends TestCase
{
    private CaptchaKeyService $service;

    protected function setUp(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);
        $stack = new RequestStack();
        $stack->push($request);
        $this->service = new CaptchaKeyService($stack);
        $session->set('CAPTCHA', [100, 100]);
    }

    public function testGoodAnswer()
    {
        $this->assertTrue($this->service->verifyKey('95-105'));
    }

    public function testBadAnswer()
    {
        $this->assertFalse($this->service->verifyKey('94-105'));
    }

    public function testLimit()
    {
        $this->expectException(TooManyTryException::class);
        $this->assertFalse($this->service->verifyKey('94-105'));
        $this->assertFalse($this->service->verifyKey('94-105'));
        $this->assertFalse($this->service->verifyKey('94-105'));
    }
}
