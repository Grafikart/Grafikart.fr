<?php

namespace App\Tests\Http\Controller\Account;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class BadgesControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testRedirectToLogin(): void
    {
        $this->client->request('GET', '/profil/badges');
        $this->assertResponseRedirects();
    }

    public function testIndex(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', '/profil/badges');
        $this->assertResponseStatusCodeSame(200);
        $this->expectH1('Mes badges');
        $this->expectTitle('Mes badges');
    }
}
