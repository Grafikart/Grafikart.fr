<?php

namespace App\Tests\Http\Admin;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class PagesControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testAdminPage(): void
    {
        $data = $this->loadFixtures(['users']);
        $this->login($data['user_admin']);
        $this->client->request('GET', '/admin/');
        $this->expectTitle('Dashboard');
    }

}
