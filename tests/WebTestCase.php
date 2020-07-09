<?php

namespace App\Tests;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Runner\Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        /** @var EntityManagerInterface $em */
        $em = self::$container->get(EntityManagerInterface::class);
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->em->clear();
        parent::tearDown();
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
     * Vérifie si on a un message d'erreur.
     */
    public function expectErrorAlert(): void
    {
        $this->assertEquals(1, $this->client->getCrawler()->filter('alert-message[type="danger"], alert-message[type="error"]')->count());
    }

    /**
     * Vérifie si on a un message de succès.
     */
    public function expectSuccessAlert(): void
    {
        $this->assertEquals(1, $this->client->getCrawler()->filter('alert-message[type="success"]')->count());
    }

    public function expectFormErrors(?int $expectedErrors = null): void
    {
        if (null === $expectedErrors) {
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

    public function expectTitle(string $title): void
    {
        $crawler = $this->client->getCrawler();
        $this->assertEquals(
            $title,
            $crawler->filter('title')->text(),
            $crawler->filter('title')->text()
        );
    }

    public function login(?User $user)
    {
        if (null === $user) {
            return;
        }
        // On récupère l'instance dans l'entityManager pour éviter la deAuthenticate dans le ContextListener
        /** @var EntityManagerInterface $em */
        $em = self::$container->get(EntityManagerInterface::class);
        $managedUser = $em->getRepository(User::class)->find($user->getId());
        if (null === $managedUser) {
            throw new Exception("Impossible de retrouver l'utilisateur {$user->getId()}");
        }

        // On crée le cookie
        $session = self::$container->get('session');
        $firewallName = 'main';
        $firewallContext = $firewallName;
        $token = new UsernamePasswordToken($managedUser, null, $firewallName, $managedUser->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());

        // On ajoute le cookie au client
        $this->client->getCookieJar()->set($cookie);
    }
}
