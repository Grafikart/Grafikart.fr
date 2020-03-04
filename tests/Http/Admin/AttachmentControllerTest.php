<?php

namespace App\Tests\Http\Admin;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AttachmentControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function filesDataProvider(): iterable
    {
        $data = $this->loadFixtures(['users', 'attachments']);
        $attachment = $data['attachment1'];
        yield [null, Response::HTTP_FORBIDDEN];
        yield [$data['user1'], Response::HTTP_FORBIDDEN];
        yield [$data['user_admin'], Response::HTTP_OK];
        yield [$data['user_admin'], Response::HTTP_OK, '/folders'];
        yield [$data['user_admin'], Response::HTTP_UNPROCESSABLE_ENTITY, '/files?path=azeaze'];
        yield [$data['user1'], Response::HTTP_FORBIDDEN, '/' . $attachment->getId(), 'DELETE'];
        yield [$data['user_admin'], Response::HTTP_OK, '/' . $attachment->getId(), 'DELETE'];
    }

    /**
     * @dataProvider filesDataProvider
     */
    public function testFilesEndpoint (?User $user, int $expectedStatus, $endpoint = '/files', $method = 'GET') {
        if ($user) {
            $this->login($user);
        }
        $this->jsonRequest($method, '/admin/attachment' . $endpoint);
        $this->assertResponseStatusCodeSame($expectedStatus);
    }

    public function testReturnRightNumberOfAttachment(): void
    {
        ['user_admin' => $admin] = $this->loadFixtures(['users', 'attachments']);
        $this->login($admin);
        $content = $this->jsonRequest('GET', '/admin/attachment/files');
        $items = json_decode($content);
        $this->assertCount(5, $items);
    }

}
