<?php

namespace App\Tests\Http\Admin;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test fonctionnel pour vérifier que l'accès à l'administraiton est bien sécurisé.
 */
class AdminSecurityTest extends WebTestCase
{
    use FixturesTrait;

    private array $users = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->users = $this->loadFixtures(['users']);
    }

    public function dataProvider(): iterable
    {
        yield [null, Response::HTTP_FOUND];
        yield ['user1', Response::HTTP_FORBIDDEN];
        yield ['user_admin', Response::HTTP_OK];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAdminAccess(?string $user, int $responseCode): void
    {
        if ($user) {
            $this->login($this->users[$user]);
        }
        $verb = 'ne devrait pas';
        if (Response::HTTP_OK === $responseCode) {
            $verb = 'devrait';
        }
        $this->client->request('GET', '/admin/');
        $this->assertResponseStatusCodeSame($responseCode, "L'utilisateur {$user} $verb pouvoir voir l'admin");
    }
}
