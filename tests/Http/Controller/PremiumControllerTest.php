<?php

namespace App\Tests\Http\Controller;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class PremiumControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testForum(): void
    {
        $title = "Devenir premium";
        $crawler = $this->client->request('GET', '/premium');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($title, $crawler->filter('h1')->text());
    }
}
