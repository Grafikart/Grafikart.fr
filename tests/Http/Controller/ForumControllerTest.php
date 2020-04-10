<?php

namespace App\Tests\Http\Controller;

use App\Domain\Forum\Entity\Category;
use App\Domain\Forum\Entity\Forum;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testForum(): void
    {
        $title = "Forum";
        $client = static::createClient();
        $crawler = $client->request('GET', '/forum');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($title, $crawler->filter('h1')->text());
    }

    /**
    public function testForumDisplayContent(): void
    {
        $client = static::createClient();
        $this->loadFixtures(['forums']);
        $crawler = $client->request('GET', '/forum');
        $this->assertEquals(4, $crawler->filter('h2')->count());
        $this->assertEquals(10, $crawler->filter('h3')->count());
    }
     **/
}
