<?php

namespace App\Tests;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;
    protected ?SessionInterface $session = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->em->clear();
        parent::tearDown();
    }

    public function jsonRequest(string $method, string $url, ?array $data = null): string
    {
        $this->client->request($method, $url, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $data ? json_encode($data, JSON_THROW_ON_ERROR) : null);

        return $this->client->getResponse()->getContent();
    }

    /**
     * Vérifie si on a un message de succès.
     */
    public function expectAlert(string $type, ?string $message = null): void
    {
        $this->assertEquals(1, $this->client->getCrawler()->filter("alert-message[type=\"$type\"], alert-floating[type=\"$type\"]")->count());
        if ($message) {
            $this->assertStringContainsString($message, $this->client->getCrawler()->filter("alert-message[type=\"$type\"], alert-floating[type=\"$type\"]")->text());
        }
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
        $this->expectAlert('success');
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
            '<h1> missmatch'
        );
    }

    public function expectTitle(string $title): void
    {
        $crawler = $this->client->getCrawler();
        $this->assertEquals(
            $title.' | Grafikart',
            $crawler->filter('title')->text(),
            '<title> missmatch',
        );
    }

    public function login(?User $user)
    {
        if (null === $user) {
            return;
        }
        $this->client->loginUser($user);
    }

    public function setCsrf(string $key): string
    {
        $csrf = uniqid();

        // Write directly into the session file cause there is no way to access the session from tests :(
        foreach($this->client->getCookieJar()->all() as $cookie) {
            if ($cookie->getName() === 'MOCKSESSID') {
                $path = self::getContainer()->getParameter('kernel.cache_dir') . '/sessions/' . $cookie->getValue() . '.mocksess';
                $file = unserialize(file_get_contents($path));
                $file['_sf2_attributes']['_csrf/' . $key] = $csrf;
                file_put_contents($path, serialize($file));
            }
        }

        return $csrf;
    }

    protected function getSession (): SessionInterface {
        if (!$this->session) {
            $container = $this->getContainer();
            $session = $container->get('session.factory')->createSession();
            $domains = array_unique(array_map(function (Cookie $cookie) use ($session) {
                return $cookie->getName() === $session->getName() ? $cookie->getDomain() : '';
            }, $this->client->getCookieJar()->all())) ?: [''];
            foreach ($domains as $domain) {
                $cookie = new Cookie($session->getName(), $session->getId(), null, null, $domain);
                $this->client->getCookieJar()->set($cookie);
            }
            $this->session = $session;
        }
        return $this->session;
    }

    protected function ensureSessionIsAvailable(): void
    {
        $container = self::getContainer();
        $requestStack = $container->get('request_stack');

        try {
            $requestStack->getSession();
        } catch (SessionNotFoundException $e) {
            $session = $container->has('session')
                ? $container->get('session')
                : $container->get('session.factory')->createSession();

            $masterRequest = new Request();
            $masterRequest->setSession($session);

            $requestStack->push($masterRequest);

            $session->start();
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
        }
    }
}
