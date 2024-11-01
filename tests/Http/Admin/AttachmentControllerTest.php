<?php

namespace App\Tests\Http\Admin;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AttachmentControllerTest extends WebTestCase
{
    use FixturesTrait;

    public array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'attachments']);
    }

    public function filesDataProvider(): iterable
    {
        yield [null, Response::HTTP_FORBIDDEN];
        yield ['user1', Response::HTTP_FORBIDDEN];
        yield ['user_admin', Response::HTTP_OK];
        yield ['user_admin', Response::HTTP_OK, '/folders'];
        yield ['user_admin', Response::HTTP_UNPROCESSABLE_ENTITY, '/files?path=azeaze'];
        yield ['user1', Response::HTTP_FORBIDDEN, '/{id}', 'DELETE'];
        yield ['user_admin', Response::HTTP_OK, '/{id}', 'DELETE'];
    }

    /**
     * @dataProvider filesDataProvider
     */
    public function testFilesEndpoint(?string $user, int $expectedStatus, string $endpoint = '/files', string $method = 'GET')
    {
        if ($user) {
            $this->login($this->data[$user]);
            $endpoint = str_replace('{id}', $this->data['attachment1']->getId(), $endpoint);
        }
        $this->jsonRequest($method, '/admin/attachment'.$endpoint);
        $this->assertResponseStatusCodeSame($expectedStatus);
    }

    public function testReturnRightNumberOfAttachment(): void
    {
        ['user_admin' => $admin] = $this->data;
        $this->login($admin);
        $response = $this->jsonRequest('GET', '/admin/attachment/files');
        $items = json_decode($response->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(5, $items);
    }
}
