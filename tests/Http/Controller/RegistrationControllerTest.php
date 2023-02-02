<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Infrastructure\Social\SocialLoginService;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use http\Client\Request;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\CssSelector\Node\ElementNode;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegistrationControllerTest extends WebTestCase
{
    use FixturesTrait;

    private const SIGNUP_PATH = '/inscription';
    private const CONFIRMATION_PATH = '/inscription/confirmation/';
    private const SIGNUP_BUTTON = "S'inscrire";

    public function testSEO(): void
    {
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals("S'inscrire", $crawler->filter('h1')->text());
        $this->assertPageTitleContains('Inscription');
    }

    public function testRegisterSendEmail(): void
    {
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'username' => 'Jane Doe',
                'email' => 'jane@doe.fr',
                'plainPassword' => [
                    'first' => 'jane@doe.fr',
                    'second' => 'jane@doe.fr',
                ],
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        $this->assertEmailCount(1);
        $this->assertResponseRedirects('/connexion');
        $this->client->followRedirect();
        $this->expectAlert('success');
    }

    public function testRegisterExistingInformations(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $formValues = [
            'registration_form' => [
                'username' => 'Jane Doe',
                'email' => $users['user1']->getEmail(),
                'plainPassword' => [
                    'first' => 'jane@doe.fr',
                    'second' => 'jane@doe.fr',
                ],
            ],
        ];
        $form->setValues($formValues);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        $this->assertEmailCount(0);
        $formValues['registration_form']['username'] = $users['user1']->getUsername();
        $form->setValues($formValues);
        $this->client->submit($form);
        $this->expectFormErrors(2);
        $this->assertEmailCount(0);
    }

    public function testRegisterExistingEmail(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $formValues = [
            'registration_form' => [
                'username' => 'Jane Doe',
                'email' => strtoupper($users['user1']->getEmail()),
                'plainPassword' => [
                    'first' => 'jane@doe.fr',
                    'second' => 'jane@doe.fr',
                ],
            ],
        ];
        $form->setValues($formValues);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        $this->assertEmailCount(0);
    }

    public function testWithLongEmail(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $formValues = [
            'registration_form' => [
                'username' => 'Jane Doe',
                'email' => 'fdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktjfdmqagnukbbiitrouoskoaffipvqkufeaaqxzgkjvzufukwectpivvmgbbvtzggogxtunaayxipvonbcacmuubkakxsnfiakuxqfdynmpfhwhjtphucuxyvhapnjbktj@gmail.com',
                'plainPassword' => [
                    'first' => 'jane@doe.fr',
                    'second' => 'jane@doe.fr',
                ],
            ],
        ];
        $form->setValues($formValues);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        $this->assertEmailCount(0);
    }

    public function testConfirmationTokenInvalid(): void
    {
        /** @var User[] $users */
        $users = $this->loadFixtures(['users']);
        $user = $users['user_unconfirmed'];

        $this->client->request('GET', self::CONFIRMATION_PATH.$user->getId().'?token=azeazeaze');
        $this->assertResponseRedirects(self::SIGNUP_PATH);
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testConfirmationTokenValid(): void
    {
        /** @var User[] $users */
        $users = $this->loadFixtures(['users']);
        $user = $users['user_unconfirmed'];
        $user->setCreatedAt(new \DateTime());
        $this->em->flush();

        $this->client->request('GET', self::CONFIRMATION_PATH.$user->getId().'?token='.$user->getConfirmationToken());
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectSuccessAlert();
    }

    public function testConfirmationTokenExpire(): void
    {
        /** @var User[] $users */
        $users = $this->loadFixtures(['users']);
        $user = $users['user_unconfirmed'];
        $user->setCreatedAt(new \DateTime('-1 day'));
        $this->em->flush();

        $this->client->request('GET', self::CONFIRMATION_PATH.$user->getId().'?token='.$user->getConfirmationToken());
        $this->assertResponseRedirects(self::SIGNUP_PATH);
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testRedirectIfLogged(): void
    {
        /** @var User[] $data */
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $this->client->request('GET', self::SIGNUP_PATH);
        $this->assertResponseRedirects('/profil');
    }

    public function testOauthRegistration(): void
    {
        // Simule un scénarion Oauth
        $this->client->request('GET', self::SIGNUP_PATH);
        $github = new GithubResourceOwner([
            'email' => 'john@doe.fr',
            'login' => 'JohnDoe',
            'id' => 123123,
        ]);
        $this->client->getContainer()->get(SocialLoginService::class)->persist($this->getSession(), $github);

        $crawler = $this->client->request('GET', self::SIGNUP_PATH.'?oauth=1');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'username' => 'Jane Doe',
            ],
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        $this->assertResponseRedirects();
        $this->assertEmailCount(0);
    }
}
