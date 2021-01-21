<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Profile\Entity\EmailVerification;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccountControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testUnauthenticatedIsRedirected(): void
    {
        $this->client->request('GET', '/profil');
        $this->assertResponseRedirects('/connexion');
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

    public function testShowErrorOnLongEmails(): void
    {
        /** @var User[] $data */
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil/edit');
        $form = $crawler->selectButton('Modifier mon profil')->form();
        $form->setValues([
            'update_profile_form[email]' => 'fdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktjfdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktj@gmail.com',
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
    }

    public function testShowErrorEmptyUsername(): void
    {
        /** @var User[] $data */
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $crawler = $this->client->request('GET', '/profil/edit');
        $form = $crawler->selectButton('Modifier mon profil')->form();
        $form->setValues([
            'update_profile_form[username]' => '   ',
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
        $this->expectAlert('info');
    }

    public function testSendEmailOnDeleteRequest(): void
    {
        /* @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->jsonRequest('DELETE', '/profil', [
            'password' => '0000',
            'csrf' => $this->setCsrf('delete-account'),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEmailCount(1);
    }

    public function testProfilePageEmptyPassword(): void
    {
        /* @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $crawler = $this->client->request('GET', '/profil/edit');
        $form = $crawler->selectButton('Modifier mon mot de passe')->form();
        $form->setValues([
            'update_password_form[password][first]' => null,
            'update_password_form[password][second]' => null,
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
    }

    /**
     * 2 utilisateurs essaient de changer leur email pour mettre le même email.
     */
    public function testCrossedEmailChanges(): void
    {
        /* @var User $user */
        /* @var User $user2 */
        ['user1' => $user, 'user2' => $user2] = $this->loadFixtures(['users']);
        $emailVerification = (new EmailVerification())
            ->setAuthor($user)
            ->setCreatedAt(new \DateTime())
            ->setEmail($user2->getEmail())
            ->setToken('hello');
        $this->em->persist($emailVerification);
        $this->em->flush();

        $this->login($user);
        $this->client->request('GET', '/email-confirm/hello');
        $this->assertResponseRedirects('/connexion');
    }
}
