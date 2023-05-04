<?php

namespace App\Tests\Http\Twig;

use App\Domain\Application\Repository\ContentRepository;
use App\Domain\Course\Entity\Course;
use App\Http\Twig\TwigMarkdownExtension;
use App\Http\Twig\ViewRendererInterface;
use PHPUnit\Framework\TestCase;

class TwigMarkdownExtensionTest extends TestCase
{

    /**
     * @param ContentRepository $mock
     */
    private function getExtension(): TwigMarkdownExtension
    {
        $contentRepository = $this->createMock(ContentRepository::class);
        $contentRepository
            ->expects($this->any())
            ->method('findOrFail')
            ->with($this->equalTo(520))
            ->willReturn(new Course());
        $renderer = $this->createMock(ViewRendererInterface::class);
        $renderer->method('render')->willReturnArgument(0);
        return new TwigMarkdownExtension($contentRepository, $renderer);
    }

    public function testExcerptWithNull(): void
    {
        $this->assertEquals('', $this->getExtension()->excerpt(null));
    }

    public function testExcerptWithShortText(): void
    {
        $extension = $this->getExtension();
        $this->assertEquals('je fais', $extension->excerpt('je fais', 4));
        $this->assertEquals('je fais un test', $extension->excerpt('je fais un test', 130));
    }

    public function testExcerptWithLongText(): void
    {
        $extension = $this->getExtension();
        $this->assertEquals('je fais...', $extension->excerpt('je fais un test', 5));
    }

    public function testMarkdown(): void
    {
        $extension = $this->getExtension();
        $this->assertEquals('<p>Salut les <strong>gens</strong></p>', $extension->markdown('Salut les **gens**'));
    }

    public function testMarkdownParseTimecodes(): void
    {
        $extension = $this->getExtension();
        $this->assertEquals('<p><a href="#t0">00:00</a> Test de sommaire<br />
<a href="#t60">01:00</a> Premier chapitre<br />
<a href="#t3860">01:04:20</a> Premier chapitre</p>', $extension->markdown(<<<MARKDOWN
00:00 Test de sommaire
01:00 Premier chapitre
01:04:20 Premier chapitre
MARKDOWN
        ));
    }

    public function testMarkdownParseContent(): void
    {
        $extension = $this->getExtension();
        $markdown = "Hello\n\n<content id=\"520\"/>\n\nword";
        $this->assertEquals("<p>Hello</p>\ncontent/_card.html.twig\n<p>word</p>", $extension->markdown($markdown));
    }

    public function testMarkdownUntrusted(): void
    {
        $extension = $this->getExtension();
        $this->assertEquals(
            '<p>Demo <a target="_blank" rel="noreferrer nofollow" href="https://grafikart.fr">Grafikart</a> Site</p>',
            $extension->markdownUntrusted('Demo [Grafikart](https://grafikart.fr) Site')
        );
        $this->assertEquals(
            '<p>Demo <a href="/tutoriel/demo">Grafikart</a> Site</p>',
            $extension->markdownUntrusted('Demo [Grafikart](/tutoriel/demo) Site')
        );
    }
}
