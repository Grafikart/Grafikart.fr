<?php

namespace App\Tests\Http\Controller;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class ForumControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testForum(): void
    {
        $title = 'Forum';
        $crawler = $this->client->request('GET', '/forum');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($title, $crawler->filter('h1')->text());
    }

    public function testCreateTopicUnauthenticated(): void
    {
        $this->client->request('GET', '/forum/new');
        $this->assertResponseRedirects('/login');
    }

    public function testCreateTopicAuthenticated(): void
    {
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $this->client->request('GET', '/forum/new');
        $this->assertResponseStatusCodeSame(200);
    }
}
