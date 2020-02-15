<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = self::createClient();
        parent::setUp();
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

}
