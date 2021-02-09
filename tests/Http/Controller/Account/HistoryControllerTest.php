<?php

declare(strict_types=1);

namespace App\Tests\Http\Controller\Account;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class HistoryControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testHistoryRedirectToLogin(): void
    {
        $this->client->request('GET', '/profil/historique');
        $this->assertResponseRedirects();
    }

    public function testHistory(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', '/profil/historique');
        $this->assertResponseStatusCodeSame(200);
        $this->expectH1('Mon historique');
        $this->expectTitle('Mon historique');
    }
}
