<?php

namespace App\Tests\Http\Controller;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class UserControllerTest extends WebTestCase
{

    use FixturesTrait;


    public function testUnauthenticatedIsRedirected(): void
    {
        $this->client->request('GET', '/profil');
        $this->assertResponseRedirects('/login');
    }

    public function testResponseIsOkWhenAuthenticated(): void
    {
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Mon profil', $crawler->filter('h1')->text(), $crawler->filter('title')->text());
    }
}
