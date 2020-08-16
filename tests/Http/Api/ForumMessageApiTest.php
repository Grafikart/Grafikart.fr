<?php

namespace App\Tests\Http\Api;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
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
        $this->client->request('DELETE', '/api/forum/messages/'.$message->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteAuthenticated(): void
    {
        $data = $this->loadFixtures(['forums']);
        /** @var Message $message */
        $message = $data['message1'];
        $this->login($message->getAuthor());
        $this->client->request('DELETE', '/api/forum/messages/'.$message->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateOwnMessage(): void
    {
        $data = $this->loadFixtures(['forums']);
        /** @var Message $message */
        $message = $data['message1'];
        $this->login($message->getAuthor());
        $this->client->request('PUT', '/api/forum/messages/'.$message->getId(), [
            'json' => [
                'content' => $message->getContent().' UPDATED',
            ],
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
        $this->client->request('PUT', '/api/forum/messages/'.$message->getId(), [
            'json' => [
                'content' => $message->getContent().' UPDATED',
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testSolveMessage(): void
    {
        $data = $this->loadFixtures(['forums']);
        /** @var Message $message */
        $message = $data['message1'];
        $this->login($message->getTopic()->getAuthor());
        $this->client->request('POST', "/api/forum/messages/{$message->getId()}/solve");
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->em->refresh($message);
        $this->assertTrue($message->isAccepted());
        $this->assertTrue($message->getTopic()->isSolved());
    }
}
