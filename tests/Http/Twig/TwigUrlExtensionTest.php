<?php

namespace App\Tests\Http\Twig;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Http\Twig\TwigUrlExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TwigUrlExtensionTest extends TestCase
{
    private TwigUrlExtension $extension;

    public function setUp(): void
    {
        parent::setUp();
        $serializer = $this->createMock(SerializerInterface::class);
        $uploaderHelper = $this->createMock(UploaderHelper::class);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->extension = new TwigUrlExtension($urlGenerator, $uploaderHelper, $serializer);
    }

    public function testAutolink(): void
    {
        $result = $this->extension->autoLink(<<<HTML
            Découverte du jour, quand vous mettez le lien d'une MR en cours en commentaire dans votre code. Github vous prévien… https://t.co/KFais4txqG
        HTML);
        $this->assertEquals(<<<HTML
            Découverte du jour, quand vous mettez le lien d'une MR en cours en commentaire dans votre code. Github vous prévien… <a href="https://t.co/KFais4txqG" target="_blank" rel="noopener noreferrer">https://t.co/KFais4txqG</a>
        HTML, $result);
    }
}
