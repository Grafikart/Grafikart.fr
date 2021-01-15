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
}
