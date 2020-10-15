<?php

namespace App\Tests\Http\Controller\Course;

use App\Domain\Course\Entity\Course;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CourseControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testShowSuccessAndRightTitle()
    {
        /** @var Course $course */
        ['course1' => $course] = $this->loadFixtures(['courses']);
        $this->client->request('GET', "/tutoriels/{$course->getSlug()}-{$course->getId()}");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Tutoriel Vidéo '.$course->getTitle());
        $this->expectH1($course->getTitle());
    }

    public function testIndexSuccess()
    {
        $this->loadFixtures(['courses']);
        $this->client->request('GET', '/tutoriels');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Tous les tutoriels');
        $this->expectH1('Tous les tutoriels');
    }

    public function testPremiumSuccess()
    {
        $this->loadFixtures(['courses']);
        $this->client->request('GET', '/tutoriels/premium');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Tous les tutoriels premiums');
        $this->expectH1('Tous les tutoriels premiums');
    }

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
