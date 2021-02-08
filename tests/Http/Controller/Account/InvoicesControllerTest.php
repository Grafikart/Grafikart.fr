<?php

declare(strict_types=1);

namespace App\Tests\Http\Controller\Account;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class InvoicesControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testIndex(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', '/profil/factures');
        $this->assertResponseStatusCodeSame(200);
        $this->expectH1('Mes factures');
        $this->expectTitle('Mes factures');
    }
}
