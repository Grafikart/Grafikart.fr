<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testUnauthenticatedIsRedirected(): void
    {
        $this->client->request('GET', '/profil');
        $this->assertResponseRedirects('/connexion');
    }

    public function testProfilePage(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->client->request('GET', "/profil/{$user->getId()}");
        $this->assertResponseStatusCodeSame(200);
    }

    public function testProfilePageWithBadId(): void
    {
        $this->client->request('GET', '/profil/azijeazejoaz');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testResponseIsOkWhenAuthenticated(): void
    {
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Mon compte', $crawler->filter('h1')->text(), $crawler->filter('title')->text());
    }

    public function testShowAlertWhenProfileUpdated(): void
    {
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil/edit');
        $form = $crawler->selectButton('Modifier mon profil')->form();

        $this->client->submit($form);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectSuccessAlert();
    }

    public function testShowAlertWhenEmailAlreadyTaken(): void
    {
        /** @var User[] $data */
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil/edit');
        $form = $crawler->selectButton('Modifier mon profil')->form();
        $form->setValues([
            'update_profile_form[email]' => $data['user2']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
    }

    public function testSendEmailOnEmailChange(): void
    {
        /** @var User[] $data */
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil/edit');
        $form = $crawler->selectButton('Modifier mon profil')->form();
        $form->setValues([
            'update_profile_form[email]' => 'john@azeazeazea.fr',
        ]);
        $this->client->submit($form);
        $this->assertEmailCount(2);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectSuccessAlert();
    }

    public function testSendEmailOnDeleteRequest(): void
    {
        /* @var User[] $data */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->jsonRequest('DELETE', '/profil', [
            'password' => '0000',
            'csrf' => $this->setCsrf('delete-account'),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEmailCount(1);
    }
}
