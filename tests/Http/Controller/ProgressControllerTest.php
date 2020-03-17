<?php

namespace App\Tests\Http\Controller;

use App\Domain\History\Entity\Progress;
use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class HistoryControllerTest extends ApiTestCase
{

    use FixturesTrait;

    public function testProgressNeedAuthentication (): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->client->request('POST', "/progress/{$course->getId()}/100");
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testProgressWithAuthenticatedUser (): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/progress/{$course->getId()}/100");
        $this->assertResponseIsSuccessful();
    }

    public function testRejectNumberAbove100 (): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/progress/{$course->getId()}/101");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testRejectNotExistingContent (): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/progress/200/101");
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testProgressUpdateDatabase (): void
    {
        $data = $this->loadFixtures(['courses']);
        $course = $data['course1'];
        $this->login($data['user1']);
        $this->client->request('POST', "/progress/{$course->getId()}/100");
        $count = self::$container->get('doctrine')->getRepository(Progress::class)->count([]);
        $this->assertEquals(1, $count);
    }

}
