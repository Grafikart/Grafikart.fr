<?php

namespace App\Tests\Http\Controller;

use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class GlossaryControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/lexique');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Lexique', $crawler->filter('h1')->text());
        $this->assertEquals('Lexique | Grafikart', $crawler->filter('title')->text());
    }
}
