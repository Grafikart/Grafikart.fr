<?php

namespace App\Tests\Twig;

use App\Twig\TwigExtension;
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
}
