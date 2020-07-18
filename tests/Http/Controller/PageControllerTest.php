<?php

namespace App\Tests\Http\Controller;

use App\Tests\WebTestCase;

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
        $this->client->request('GET', '/a-propos');
        $this->expectTitle($title);
        $this->expectH1($title);
    }
}
