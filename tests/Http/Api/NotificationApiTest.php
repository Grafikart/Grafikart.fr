<?php

namespace App\Tests\Http\Api;

use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use App\Tests\Infrastructure\Mercure\MercureAssertions;
use Symfony\Component\HttpFoundation\Response;

class NotificationApiTest extends ApiTestCase
{
    use FixturesTrait;
    use MercureAssertions;

    public function notificationData(): iterable
    {
        yield [
            [
                'message' => 'Hello world',
                'channel' => 'global',
                'url' => 'https://grafikart.fr/grafikart/live',
            ],
        ];
    }

    /**
     * @dataProvider notificationData
     */
    public function testCreateUnauthenticated(array $data): void
    {
        $this->client->request('POST', '/api/notifications', ['json' => $data]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider notificationData
     */
    public function testCreateBadRequest(array $data): void
    {
        $data['url'] = 'azeazeeazaz';
        $users = $this->loadFixtures(['users']);
        $this->login($users['user_admin']);
        $this->client->request('POST', '/api/notifications', [
            'json' => $data,
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @dataProvider notificationData
     */
    public function testCreateOk(array $data): void
    {
        $users = $this->loadFixtures(['users']);
        $this->login($users['user_admin']);
        $this->client->request('POST', '/api/notifications', [
            'json' => $data,
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * @dataProvider notificationData
     */
    public function testCreatePublishOnMercure(array $data): void
    {
        $users = $this->loadFixtures(['users']);
        $this->login($users['user_admin']);
        $this->client->request('POST', '/api/notifications', [
            'json' => $data,
        ]);
        $this->assertPublishedOnTopic('/notifications/global');
    }
}
