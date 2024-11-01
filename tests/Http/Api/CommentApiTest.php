<?php

namespace App\Tests\Http\Api;

use App\Domain\Auth\User;
use App\Domain\Comment\Entity\Comment;
use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpFoundation\Response;

class CommentApiTest extends ApiTestCase
{
    use FixturesTrait;
    use MatchesSnapshots;

    public function testGetWithoutContent()
    {
        $this->jsonRequest('GET', '/api/comments');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetWithContent()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $contentId = $fixtures['post1']->getId();
        $response = $this->jsonRequest('GET', "/api/comments?content=$contentId");
        $this->assertResponseIsSuccessful();
        $this->assertCount(7, json_decode($response->getContent(), true));
    }

    public function testDeleteWithoutAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $comment = $fixtures['comment_user']->getId();
        $this->jsonRequest('DELETE', "/api/comments/$comment");
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->login($fixtures['user2']);
        $this->jsonRequest('DELETE', "/api/comments/$comment");
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateWithBadData()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->jsonRequest('POST', '/api/comments', [
            'content' => 'Hel',
            'username' => 'John Doe'.time(),
            'target' => $fixtures['post1']->getId(),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'content',
                ],
            ],
        ]);
    }

    public function testCreateWithEmptyComment()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->jsonRequest('POST', '/api/comments', [
            'content' => '         ',
            'username' => '        ',
            'target' => $fixtures['post1']->getId(),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                ],
                [
                    'propertyPath' => 'content',
                ],
            ],
        ]);
    }

    public function testCreateWithGoodData()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->jsonRequest('POST', '/api/comments', [
            'content' => 'Hello world !',
            'username' => 'John Doe',
            'target' => $fixtures['post1']->getId(),
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testCreateWithUsedUsername()
    {
        $fixtures = $this->loadFixtures(['comments', 'users']);
        /** @var User $user */
        $user = $fixtures['user1'];
        $this->jsonRequest('POST', '/api/comments', [
            'content' => 'Hello world !',
            'username' => $user->getUsername(),
            'target' => $fixtures['post1']->getId(),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'username',
                ],
            ],
        ]);
    }

    public function testCreateWithoutAuthFail()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->jsonRequest('POST', '/api/comments', [
            'content' => 'Hello world !',
            'target' => $fixtures['post1']->getId(),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateWithAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $this->login($fixtures['user1']);
        $this->jsonRequest('POST', '/api/comments', [
            'content' => 'Hello world !',
            'target' => $fixtures['post1']->getId(),
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteWithGoodAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        /** @var Comment $comment */
        $comment = $fixtures['comment_user'];
        $this->login($fixtures['user1']);
        $this->jsonRequest('DELETE', "/api/comments/{$comment->getId()}");
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteWithBadId()
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->jsonRequest('DELETE', '/api/comments/100');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateWithBadAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        $comment = $fixtures['comment1'];
        $this->jsonRequest('PUT', "/api/comments/{$comment->getId()}", [
            'content' => 'Hello world !',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateWithGoodAuth()
    {
        $fixtures = $this->loadFixtures(['comments']);
        /** @var Comment $comment */
        $comment = $fixtures['comment_user'];
        $this->login($fixtures['user1']);
        $this->jsonRequest('PUT', "/api/comments/{$comment->getId()}", [
            'content' => 'Hello world !',
        ]);
        $this->assertResponseIsSuccessful();
    }
}
