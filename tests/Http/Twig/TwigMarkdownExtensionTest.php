<?php

namespace App\Tests\Http\Twig;

use App\Domain\Application\Repository\ContentRepository;
use App\Domain\Course\Entity\Course;
use App\Http\Twig\TwigMarkdownExtension;
use App\Http\Twig\ViewRendererInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class TwigMarkdownExtensionTest extends TestCase
{

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @param ContentRepository $mock
     */
    private function getExtension(
        mixed $mock = null): TwigMarkdownExtension
    {
        $mock ??= \Mockery::mock(ContentRepository::class);
        $renderer = \Mockery::mock(ViewRendererInterface::class)->shouldReceive('render')->withAnyArgs()->andReturnUsing(fn (string $view) => $view)->getMock();
        return new TwigMarkdownExtension($mock, $renderer);
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

    public function testMarkdownParseContent()
    {
        $mock = \Mockery::mock(ContentRepository::class);
        $mock->expects()->findOrFail(520)->once()->andReturns(new Course());
        $extension = $this->getExtension($mock);
        $markdown = "Hello\n\n<content id=\"520\"/>\n\nword";
        $this->assertEquals("<p>Hello</p>\ncontent/_card.html.twig\n<p>word</p>", $extension->markdown($markdown));
    }

    public function testMarkdownUntrusted()
    {
        $extension = $this->getExtension();
        $this->assertEquals('<p>Demo <a target="_blank" rel="noreferrer nofollow" href="https://grafikart.fr">Grafikart</a> Site</p>', $extension->markdownUntrusted('Demo [Grafikart](https://grafikart.fr) Site'));
        $this->assertEquals('<p>Demo <a href="/tutoriel/demo">Grafikart</a> Site</p>', $extension->markdownUntrusted('Demo [Grafikart](/tutoriel/demo) Site'));
    }
}
