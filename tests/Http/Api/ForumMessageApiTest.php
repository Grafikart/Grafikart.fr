<?php

namespace App\Tests\Http\Api;

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
}
