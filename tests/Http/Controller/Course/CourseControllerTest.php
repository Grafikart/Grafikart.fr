<?php

namespace App\Tests\Http\Controller\Course;

use App\Domain\Auth\User;
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
        ['course_with_technology' => $course] = $this->loadFixtures(['courses']);
        $this->client->request('GET', "/tutoriels/{$course->getSlug()}-{$course->getId()}");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $technologies = collect($course->getMainTechnologies())->map->getName()->implode(' & ');
        $this->expectTitle("Tutoriel vidéo {$technologies} : {$course->getTitle()}");
        $this->expectH1("Tutoriel {$technologies} : ".$course->getTitle());
    }

    public function testIndexSuccess()
    {
        $this->loadFixtures(['courses']);
        $this->client->request('GET', '/tutoriels');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Tous les tutoriels');
        $this->expectH1('Tous les tutoriels');
    }

    public function test400OnBadParameters()
    {
        $this->loadFixtures(['courses']);
        $this->client->request('GET', '/tutoriels?page=0');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->client->request('GET', '/tutoriels?page=1');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->client->request('GET', '/tutoriels?level=4');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->client->request('GET', '/tutoriels?level=azeaze');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
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
        $this->assertResponseRedirects('/connexion');
    }

    public function testDownloadVideoAuthenticatedWithoutPremium(): void
    {
        /** @var Course $course */
        /** @var User $user */
        ['user1' => $user, 'course1' => $course] = $this->loadFixtures(['courses', 'users']);
        $this->login($user);
        $this->client->request('GET', "/tutoriels/{$course->getId()}/video");
        $this->assertResponseRedirects('/premium');
    }

    public function testRedirectLegacyCourses(): void
    {
        /** @var Course $course */
        ['course1' => $course] = $this->loadFixtures(['courses']);
        $this->client->request(
            'GET',
            "/tutoriels/mysql/{$course->getSlug()}-{$course->getId()}"
        );
        $this->assertResponseRedirects(
            "/tutoriels/{$course->getSlug()}-{$course->getId()}",
            Response::HTTP_MOVED_PERMANENTLY
        );
    }
}
