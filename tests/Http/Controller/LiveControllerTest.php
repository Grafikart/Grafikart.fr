<?php

namespace App\Tests\Http\Controller;

use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LiveControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function setUp(): void
    {
        $this->markTestIncomplete();
    }

    public function testLive(): void
    {
        $title = 'Revoir les précédents lives';
        $client = static::createClient();
        $this->loadFixtures(['lives']);
        $crawler = $client->request('GET', '/live');
        $this->assertEquals($title, $crawler->filter('h1')->text());
        $this->assertEquals(10, $crawler->filter('.live')->count());
    }
}
