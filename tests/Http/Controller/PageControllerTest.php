<?php

namespace App\Tests\Http\Controller;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    public function testPolitiqueConfidentialite(): void
    {
        $title = 'Politique de confidentialité';
        $this->client->request('GET', '/politique-de-confidentialite');
        $this->expectTitle($title);
        $this->expectH1($title);
    }

    public function testMonEnvironnement(): void
    {
        $title = 'Mon environnement';
        $this->client->request('GET', '/env');
        $this->expectTitle($title);
        $this->expectH1($title);
    }

    public function testAProposRedirect(): void
    {
        $this->client->request('GET', '/a-propos');
        $this->assertResponseRedirects('/env', Response::HTTP_MOVED_PERMANENTLY);
    }
}
