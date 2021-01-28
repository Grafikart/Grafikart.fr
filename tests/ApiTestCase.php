<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
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
            'content-type' => ['application/json'],
        ],
        'body' => '',
        'json' => null,
        'base_uri' => 'http://grafikart.localhost:8000',
    ];
    protected Client $client;

    protected EntityManagerInterface $em;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        /** @var EntityManagerInterface $em */
        $em = self::$container->get(EntityManagerInterface::class);
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->client->setDefaultOptions(self::DEFAULT_OPTIONS);
    }

    public function login(User $user)
    {
        $session = self::$container->get('session');
        $firewallName = 'main';
        $firewallContext = $firewallName;
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
