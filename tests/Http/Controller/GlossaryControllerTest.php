<?php

namespace App\Tests\Http\Controller;

use App\Domain\Glossary\Entity\GlossaryItem;
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

    public function testSingle(): void
    {
        /** @var GlossaryItem $item */
        ['glossary_item1' => $item] = $this->loadFixtures(['glossary']);
        $crawler = $this->client->request('GET', '/lexique/' . $item->getSlug());
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($item->getName(), $crawler->filter('h1')->text());
        $this->assertEquals("Lexique : {$item->getName()} | Grafikart", $crawler->filter('title')->text());
    }

    public function testSingleNotFound(): void
    {
        $this->client->request('GET', '/lexique/demo-test');
        $this->assertResponseStatusCodeSame(404);
    }
}
