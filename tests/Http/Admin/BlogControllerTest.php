<?php

namespace App\Tests\Http\Admin;

use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlogControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testAccessDeniedBlog(): void
    {
        $this->client->request('GET', '/admin/blog');
        $this->assertResponseRedirects('/login');
    }

    public function testAccessDeniedForUserBlog(): void
    {
        $users = $this->loadFixtures(['users']);
        $this->login($users['user1']);
        $this->client->request('GET', '/admin/blog');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessLimitedToAdminBlog(): void
    {
        $users = $this->loadFixtures(['users']);
        $this->login($users['user_admin']);
        $this->client->request('GET', '/admin/blog');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
