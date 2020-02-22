<?php

namespace App\Tests\Http\Admin;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AttachmentControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testQueryParameterValidation(): void
    {
        $this->jsonRequest('GET', '/admin/attachment/files');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        ['user1' => $admin] = $this->loadFixtures(['users', 'attachments']);
        $this->login($admin);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testPreventDeleteIfAttachmentIsInContent(): void
    {
        $users = $this->loadFixtures(['users']);
        $this->login($users['user_admin']);
        $this->jsonRequest('GET', '/admin/attachment/files');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testReturnRightNumberOfAttachment(): void
    {
        ['user_admin' => $admin] = $this->loadFixtures(['users', 'attachments']);
        $this->login($admin);
        $content = $this->jsonRequest('GET', '/admin/attachment/files');
        $items = json_decode($content);
        $this->assertCount(5, $items);
    }

    public function testDeleteAttachment(): void
    {
        ['attachment1' => $attachment] = $this->loadFixtures(['attachments']);
        $this->jsonRequest('DELETE', '/admin/attachment/' . $attachment->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteWithRightPermission(): void
    {
        ['attachment1' => $attachment, 'user_admin' => $admin] = $this->loadFixtures(['attachments', 'users']);
        $this->login($admin);
        $this->jsonRequest('DELETE', "/admin/attachment/{$attachment->getId()}");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
