<?php

namespace App\Tests\Http\Api;

use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use App\Tests\Infrastructure\Mercure\MercureAssertions;
use Symfony\Component\HttpFoundation\Response;

class ForumReportApiTest extends ApiTestCase
{
    use FixturesTrait;
    use MercureAssertions;

    public function reportData(): iterable
    {
        yield [
            [
                'reason' => 'Il est trop méchant',
            ],
        ];
    }

    public function badReportData(): iterable
    {
        yield [
            [
                'reason' => 'az',
            ],
        ];
        yield [
            [
                'reason' => str_repeat('a', 300),
            ],
        ];
    }

    /**
     * @dataProvider reportData
     */
    public function testCreateUnauthenticated(array $data): void
    {
        $this->jsonRequest('POST', '/api/forum/reports', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider reportData
     */
    public function testCreateBadRequest(array $data): void
    {
        $data['topic'] = 'azeazeeazaz';
        $fixtures = $this->loadFixtures(['users']);
        $this->login($fixtures['user1']);
        $this->jsonRequest('POST', '/api/forum/reports', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @dataProvider badReportData
     */
    public function testCheckReportContent(array $data): void
    {
        $fixtures = $this->loadFixtures(['users', 'forums']);
        $data = array_merge($data, [
            'topic' => $fixtures['topic1']->getId(),
        ]);
        $this->login($fixtures['user1']);
        $this->jsonRequest('POST', '/api/forum/reports', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @dataProvider reportData
     */
    public function testCreateOkForTopic(array $data): void
    {
        $fixtures = $this->loadFixtures(['users', 'forums']);
        $data = array_merge($data, [
            'topic' => $fixtures['topic1']->getId(),
        ]);
        $this->login($fixtures['user1']);
        $this->jsonRequest('POST', '/api/forum/reports', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * @dataProvider reportData
     */
    public function testCreateOkForMessage(array $data): void
    {
        $fixtures = $this->loadFixtures(['users', 'forums']);
        $data = array_merge($data, [
            'message' => $fixtures['message1']->getId(),
        ]);
        $this->login($fixtures['user1']);
        $this->jsonRequest('POST', '/api/forum/reports', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
