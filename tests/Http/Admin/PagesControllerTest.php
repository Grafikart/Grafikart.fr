<?php

namespace App\Tests\Http\Admin;

use App\Tests\WebTestCase;

class PagesControllerTest extends WebTestCase
{

    public function testAdminPage () {
        // TODO : Bloquer l'accès aux utilisateurs autres qu'admins ;)
        $this->client->request('GET', '/admin/');
        $this->expectH1('Dashboard');
    }

}
