<?php

namespace App\Tests\Core\Twig;

use App\Core\Twig\TwigExtension;
use PHPUnit\Framework\TestCase;

class TwigExtensionTest extends TestCase
{
    public function testExcerptWithNull(): void
    {
        $extension = new TwigExtension();
        $this->assertEquals('', $extension->excerpt(null));
    }

    public function testExcerptWithShortText(): void
    {
        $extension = new TwigExtension();
        $this->assertEquals('je fais', $extension->excerpt('je fais', 4));
        $this->assertEquals('je fais un test', $extension->excerpt('je fais un test', 130));
    }

    public function testExcerptWithLongText(): void
    {
        $extension = new TwigExtension();
        $this->assertEquals('je fais...', $extension->excerpt('je fais un test', 5));
    }

    public function testMarkdown(): void
    {
        $extension = new TwigExtension();
        $this->assertEquals('<p>Salut les <strong>gens</strong></p>', $extension->markdown('Salut les **gens**'));
    }

    public function testMarkdownParseTimecodes(): void
    {
        $extension = new TwigExtension();
        $this->assertEquals('<p><a href="#t0">00:00</a> Test de sommaire<br />
<a href="#t60">01:00</a> Premier chapitre<br />
<a href="#t3860">01:04:20</a> Premier chapitre</p>', $extension->markdown(<<<MARKDOWN
00:00 Test de sommaire
01:00 Premier chapitre
01:04:20 Premier chapitre
MARKDOWN
        ));
    }
}
