<?php

namespace App\Tests;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    public function setUp(): void
    {
        $this->client = self::createClient();
        /** @var EntityManagerInterface $em */
        $em = self::$container->get(EntityManagerInterface::class);
        $this->em = $em;
        parent::setUp();
    }

    public function jsonRequest(string $method, string $url): string
    {
        $this->client->request($method, $url, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Accept' => 'application/json',
        ]);
        return $this->client->getResponse()->getContent();
    }

    /**
     * Vérifie si on a un message d'erreur
     */
    public function expectErrorAlert(): void
    {
        $this->assertEquals(1, $this->client->getCrawler()->filter('alert-message[type="danger"], alert-message[type="error"]')->count());
    }

    /**
     * Vérifie si on a un message de succès
     */
    public function expectSuccessAlert(): void
    {
        $this->assertEquals(1, $this->client->getCrawler()->filter('alert-message[type="success"]')->count());
    }

    public function expectFormErrors(?int $expectedErrors = null): void
    {
        if ($expectedErrors === null) {
            $this->assertTrue($this->client->getCrawler()->filter('.form-error')->count() > 0, 'Form errors missmatch.');
        } else {
            $this->assertEquals($expectedErrors, $this->client->getCrawler()->filter('.form-error')->count(), 'Form errors missmatch.');
        }
    }

    public function expectH1(string $title): void
    {
        $crawler = $this->client->getCrawler();
        $this->assertEquals(
            $title,
            $crawler->filter('h1')->text(),
            $crawler->filter('title')->text()
        );
    }

    public function login(User $user)
    {
        $session = self::$container->get('session');
        $firewallName = 'main';
        $firewallContext = $firewallName;
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

}
