<?php

namespace App\Tests\Http\Api;

use App\Domain\Comment\Comment;
use App\Http\Api\Resource\CommentResource;
use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class CommentApiTest extends ApiTestCase
{

    use FixturesTrait;

    public function testGetWithoutContent()
    {
        $this->client->request('GET', '/api/comments');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testGetWithContent()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $contentId = $fixtures['post1']->getId();
        $response = $this->client->request('GET', '/api/comments?content=' . $contentId);
        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $response->toArray());
        $this->assertMatchesResourceCollectionJsonSchema(CommentResource::class);
    }

    public function testDeleteWithoutAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $comment = $fixtures['comment_user']->getId();
        $this->client->request('DELETE', "/api/comments/$comment");
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->login($fixtures['user2']);
        $this->client->request('DELETE', "/api/comments/$comment");
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateWithBadData()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->client->request('POST', '/api/comments', [
            'json' => [
                'content'  => 'Hello world !',
                'email'    => 'johnfake',
                'username' => 'John Doe',
                'target'   => $fixtures['post1']->getId(),
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            'violations' => [[
                'propertyPath' => 'email',
            ]]
        ]);
    }

    public function testCreateWithGoodData()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->client->request('POST', '/api/comments', [
            'json' => [
                'content'  => 'Hello world !',
                'email'    => 'john@fake.fr',
                'username' => 'John Doe',
                'target'   => $fixtures['post1']->getId(),
            ]
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testCreateWithoutAuthFail()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->client->request('POST', '/api/comments', [
            'json' => [
                'content' => 'Hello world !',
                'target'  => $fixtures['post1']->getId(),
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateWithAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->login($fixtures['user1']);
        $this->client->request('POST', '/api/comments', [
            'json' => [
                'content' => 'Hello world !',
                'target'  => $fixtures['post1']->getId(),
            ]
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteWithGoodAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        /** @var Comment $comment */
        $comment = $fixtures['comment_user'];
        $this->login($fixtures['user1']);
        $this->client->request('DELETE', "/api/comments/{$comment->getId()}");
        $this->assertResponseIsSuccessful();
    }

    public function testUpdateWithBadAuth () {
        $fixtures = $this->loadFixtures(['comments']);
        $comment = $fixtures['comment1'];
        $this->client->request('PUT', "/api/comments/{$comment->getId()}", [
            'json' => [
                'content' => 'Hello world !'
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateWithGoodAuth () {
        $fixtures = $this->loadFixtures(['comments']);
        /** @var Comment $comment */
        $comment = $fixtures['comment_user'];
        $this->login($fixtures['user1']);
        $this->client->request('PUT', "/api/comments/{$comment->getId()}", [
            'json' => [
                'content' => 'Hello world !'
            ]
        ]);
        $this->assertResponseIsSuccessful();
    }

}
