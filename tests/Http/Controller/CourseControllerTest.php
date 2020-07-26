<?php

namespace App\Tests\Http\Controller;

use App\Domain\Course\Entity\Course;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class CourseControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testDownloadVideoUnauthenticated(): void
    {
        $data = $this->loadFixtures(['courses']);
        /** @var Course $course */
        $course = $data['course1'];
        $this->client->request('GET', "/tutoriels/{$course->getId()}/video");
        $this->assertResponseRedirects('/login');
    }

    public function testDownloadVideoAuthenticatedWithoutPremium(): void
    {
        $data = $this->loadFixtures(['courses']);
        $this->login($data['user1']);
        /** @var Course $course */
        $course = $data['course1'];
        $this->client->request('GET', "/tutoriels/{$course->getId()}/video");
        $this->assertResponseRedirects('/premium');
    }
}
