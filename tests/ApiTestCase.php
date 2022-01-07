<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;

class ApiTestCase extends \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase
{
    public const DEFAULT_OPTIONS = [
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
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->client->setDefaultOptions(self::DEFAULT_OPTIONS);
    }

    /**
     * En attendant le merge de pour avoir accès à la méthode loginUser sur client directement
     * https://github.com/api-platform/core/pull/4588
     */
    public function login(User $user): void
    {
        $this->client->getKernelBrowser()->loginUser($user);
    }
}
