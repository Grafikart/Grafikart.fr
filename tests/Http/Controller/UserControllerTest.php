<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testProfilePage(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->client->request('GET', "/profil/{$user->getId()}");
        $this->assertResponseStatusCodeSame(200);
    }

    public function testProfilePageWithBadId(): void
    {
        $this->client->request('GET', '/profil/azijeazejoaz');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSwitchUser(): void
    {
        /** @var User $user1 */
        /** @var User $admin */
        ['user1' => $user1, 'user_admin' => $admin] = $this->loadFixtures(['users']);
        // Un utilisateur normal ne peut pas devenir admin
        $this->login($user1);
        $this->client->request('GET', "/?_ninja={$admin->getUsername()}");
        $this->assertResponseStatusCodeSame(403);

        // Un admin peut devenir user
        $this->login($admin);
        $this->client->request('GET', "/?_ninja={$user1->getUsername()}");
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects();
    }
}
