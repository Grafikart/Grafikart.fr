<?php

namespace App\Tests\Http\Api;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use App\Tests\Infrastructure\Mercure\MercureAssertions;
use Symfony\Component\HttpFoundation\Response;

class ForumMessageApiTest extends ApiTestCase
{
    use FixturesTrait;
    use MercureAssertions;

    public function testDeleteUnauthenticated(): void
    {
        $data = $this->loadFixtures(['forums']);
        /** @var Message $message */
        $message = $data['message1'];
        $this->jsonRequest('DELETE', '/api/forum/messages/'.$message->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteAuthenticated(): void
    {
        $data = $this->loadFixtures(['forums']);
        /** @var Message $message */
        $message = $data['message1'];
        $this->login($message->getAuthor());
        $this->jsonRequest('DELETE', '/api/forum/messages/'.$message->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testCreateAuthenticated(): void
    {
        ['topic_recent' => $topic] = $this->loadFixtures(['forums']);
        $this->login($topic->getAuthor());
        $this->jsonRequest('POST', "/api/forum/topics/{$topic->getId()}/messages", [
            'content' => 'Some random content to test',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateWithEmptyContent(): void
    {
        ['topic_recent' => $topic] = $this->loadFixtures(['forums']);
        $this->login($topic->getAuthor());
        $this->jsonRequest('POST', "/api/forum/topics/{$topic->getId()}/messages", [
            'content' => '',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLockOldTopic(): void
    {
        /** @var Topic $topic */
        ['topic_old' => $topic] = $this->loadFixtures(['forums']);
        $this->login($topic->getAuthor());
        $this->jsonRequest('POST', "/api/forum/topics/{$topic->getId()}/messages", [
            'content' => 'Some random content to test',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateOwnMessage(): void
    {
        $data = $this->loadFixtures(['forums']);
        /** @var Message $message */
        $message = $data['message1'];
        $this->login($message->getAuthor());
        $this->jsonRequest('PUT', '/api/forum/messages/'.$message->getId(), [
            'content' => $message->getContent().' UPDATED',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testForbiddenUpdateNotOwnMessage(): void
    {
        $fixtures = $this->loadFixtures(['users', 'forums']);
        /** @var User $user1 */
        $user1 = $fixtures['user1'];

        /** @var User $user2 */
        $user2 = $fixtures['user2'];

        /** @var Message $message */
        $message = $fixtures['message1'];

        $message->setAuthor($user1);
        $this->login($user2);
        $this->jsonRequest('PUT', '/api/forum/messages/'.$message->getId(), [
            'content' => $message->getContent().' UPDATED',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testSolveMessage(): void
    {
        $data = $this->loadFixtures(['forums']);
        /** @var Message $message */
        $message = $data['message1'];
        $this->login($message->getTopic()->getAuthor());
        $this->jsonRequest('POST', "/api/forum/messages/{$message->getId()}/solve");
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->em->refresh($message);
        $this->assertTrue($message->isAccepted());
        $this->assertTrue($message->getTopic()->isSolved());
    }
}
