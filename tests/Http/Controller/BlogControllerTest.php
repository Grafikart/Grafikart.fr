<?php

namespace App\Tests\Http\Controller;

use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class BlogControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testIndex(): void
    {
        $this->loadFixtures(['posts']);
        $crawler = $this->client->request('GET', '/blog');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Blog', $crawler->filter('h1')->text());
        $this->assertEquals('Blog | Grafikart', $crawler->filter('title')->text());
    }

    public function testIndexPage(): void
    {
        $this->loadFixtures(['posts']);
        $crawler = $this->client->request('GET', '/blog?page=2');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Blog, page 2', $crawler->filter('h1')->text());
        $this->assertEquals('Blog, page 2 | Grafikart', $crawler->filter('title')->text());
    }

    public function testCategory(): void
    {
        $data = $this->loadFixtures(['posts']);
        /** @var Category $category */
        $category = $data['category2'];
        $crawler = $this->client->request('GET', '/blog/category/' . $category->getSlug());
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($category->getName() . ' | Grafikart', $crawler->filter('title')->text());
        $this->assertEquals($category->getName(), $crawler->filter('h1')->text());
    }

    public function testSingle(): void
    {
        $posts = $this->loadFixtures(['posts']);
        /** @var Post $post */
        $post = $posts['post2'];
        $crawler = $this->client->request('GET', '/blog/' . $post->getSlug());
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($post->getTitle() . ' | Grafikart', $crawler->filter('title')->text());
        $this->assertEquals($post->getTitle(), $crawler->filter('h1')->text());
    }

}
