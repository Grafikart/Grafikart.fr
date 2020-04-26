<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class UserControllerTest extends WebTestCase
{

    use FixturesTrait;


    public function testUnauthenticatedIsRedirected(): void
    {
        $this->client->request('GET', '/profil');
        $this->assertResponseRedirects('/login');
    }

    public function testResponseIsOkWhenAuthenticated(): void
    {
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil');
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Mon profil', $crawler->filter('h1')->text(), $crawler->filter('title')->text());
    }

    public function testShowAlertWhenProfileUpdated(): void
    {
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil');
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
        $crawler = $this->client->request('GET', '/profil');
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
        $crawler = $this->client->request('GET', '/profil');
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

}
