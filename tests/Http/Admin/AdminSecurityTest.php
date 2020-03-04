<?php

namespace App\Tests\Http\Admin;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test fonctionnel pour vérifier que l'accès à l'administraiton est bien sécurisé
 */
class AdminSecurityTest extends WebTestCase
{

    use FixturesTrait;

    public function dataProvider(): iterable
    {
        $users = $this->loadFixtures(['users']);
        yield [null, Response::HTTP_FOUND];
        yield [$users['user1'], Response::HTTP_FORBIDDEN];
        yield [$users['user_admin'], Response::HTTP_OK];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAdminAccess(?User $user, int $responseCode): iterable
    {
        if ($user) {
            $this->login($user);
        }
        $this->client->request('GET', '/admin/');
        $this->assertResponseStatusCodeSame($responseCode);
    }

}
