<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Domain\Auth\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ApiTestCase extends \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase
{

    const DEFAULT_OPTIONS = [
        'auth_basic' => null,
        'auth_bearer' => null,
        'query' => [],
        'headers' => [
            'accept' => ['application/json'],
            'content-type' => ['application/json']
        ],
        'body' => '',
        'json' => null,
        'base_uri' => 'http://grafikart.localhost:8000',
    ];
    protected Client $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->setDefaultOptions(self::DEFAULT_OPTIONS);
    }

    public function login(User $user)
    {
        $session = self::$container->get('session');
        $firewallName = 'main';
        $firewallContext = $firewallName;
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        // TODO : En attendant le merge de https://github.com/api-platform/core/pull/3418
        $this->client->getKernelBrowser()->getCookieJar()->set($cookie);
    }

}
