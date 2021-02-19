<?php

namespace App\Tests\Http\Api;

use App\Domain\History\Entity\Progress;
use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class ProgressControllerTest extends ApiTestCase
{
    use FixturesTrait;

    public function testProgressNeedAuthentication(): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->client->request('POST', "/api/progress/{$course->getId()}/100");
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testProgressWithAuthenticatedUser(): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/api/progress/{$course->getId()}/1000");
        $this->assertResponseIsSuccessful();
    }

    public function testProgressCantGoBack(): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/api/progress/{$course->getId()}/1000");
        $this->assertResponseIsSuccessful();
        $this->client->request('POST', "/api/progress/{$course->getId()}/100");
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRejectNumberAbove100(): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/api/progress/{$course->getId()}/1001");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testRejectNumberIs0(): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/api/progress/{$course->getId()}/0");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testRejectNotExistingContent(): void
    {
        $data = $this->loadFixtures(['courses']);
        $this->login($data['user1']);
        $this->client->request('POST', '/api/progress/200/101');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testProgressUpdateDatabase(): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/api/progress/{$course->getId()}/1000");
        $count = self::$container->get('doctrine')->getRepository(Progress::class)->count([]);
        $this->assertEquals(1, $count);
    }

    public function testDeleteSuccess()
    {
        /** @var Progress $progress */
        ['progress' => $progress] = $this->loadFixtures(['progress']);
        $this->login($progress->getAuthor());
        $this->client->request('DELETE', '/api/progress/'.$progress->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDeleteFailUnauthenticated()
    {
        /** @var Progress $progress */
        ['progress' => $progress] = $this->loadFixtures(['progress']);
        $this->client->request('DELETE', '/api/progress/'.$progress->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteFailOnBadUser()
    {
        /** @var Progress $progress */
        /** @var Progress $user */
        ['progress' => $progress, 'user2' => $user] = $this->loadFixtures(['progress']);
        $this->login($user);
        $this->client->request('DELETE', '/api/progress/'.$progress->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
