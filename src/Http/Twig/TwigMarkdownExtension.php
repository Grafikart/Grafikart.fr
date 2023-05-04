<?php

namespace App\Http\Twig;

use App\Domain\Application\Repository\ContentRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigMarkdownExtension extends AbstractExtension
{
    public function __construct(
        private ContentRepository $contentRepository,
        private ViewRendererInterface $renderer
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', $this->excerpt(...)),
            new TwigFilter('markdown', $this->markdown(...), ['is_safe' => ['html']]),
            new TwigFilter('markdown_excerpt', $this->markdownExcerpt(...), ['is_safe' => ['html']]),
            new TwigFilter('markdown_untrusted', $this->markdownUntrusted(...), ['is_safe' => ['html']]),
        ];
    }

    /**
     * Renvoie un extrait d'un texte.
     */
    public function excerpt(?string $content, int $characterLimit = 135): string
    {
        if (null === $content) {
            return '';
        }
        if (mb_strlen($content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos($content, ' ', $characterLimit);
        if (false === $lastSpace) {
            return $content;
        }

        return substr($content, 0, $lastSpace).'...';
    }

    /**
     * Convertit le contenu markdown en HTML.
     */
    public function markdown(?string $content): string
    {
        if (null === $content) {
            return '';
        }
        $content = (new \Parsedown())->setBreaksEnabled(true)->setSafeMode(false)->text($content);

        // On remplace les liens youtube par un embed
        $content = (string) preg_replace(
            '/<p><a href\="(http|https):\/\/www.youtube.com\/watch\?v=([^\""]+)">[^<]*<\/a><\/p>/',
            '<iframe width="560" height="315" src="//www.youtube-nocookie.com/embed/$2" frameborder="0" allowfullscreen=""></iframe>',
            (string) $content
        );
        // Spoiler tag
        $content = (string) preg_replace(
            '/<p>!!<\/p>/',
            '<spoiler-box>',
            (string) $content
        );
        $content = (string) preg_replace(
            '/<p>\/!!<\/p>/',
            '</spoiler-box>',
            (string) $content
        );
        // On ajoute des liens sur les nombres représentant un timestamp "00:01"
        $content = preg_replace_callback('/((\d{2}:){1,2}\d{2}) ([^<]*)/', function ($matches) {
            $times = array_reverse(explode(':', $matches[1]));
            $title = $matches[3];
            $timecode = (int) ($times[2] ?? 0) * 60 * 60 + (int) $times[1] * 60 + (int) $times[0];

            return "<a href=\"#t{$timecode}\">{$matches[1]}</a> $title";
        }, $content) ?: $content;

        // On génère les carte pour les contenus
        $content = preg_replace_callback('/<content id="(\d+)".*?\/?>/i', function ($matches) {
            $content = $this->contentRepository->findOrFail((int)$matches[1]);
            return $this->renderer->render('content/_card.html.twig', [
                'content' => $content
            ]);
        }, $content) ?: $content;

        return $content;
    }

    public function markdownExcerpt(?string $content, int $characterLimit = 135): string
    {
        return $this->excerpt(strip_tags($this->markdown($content)), $characterLimit);
    }

    public function markdownUntrusted(?string $content): string
    {
        $content = strip_tags((string) (new \Parsedown())
            ->setSafeMode(true)
            ->setBreaksEnabled(true)
            ->text($content), '<p><pre><code><ul><ol><li><h4><h3><h5><a><strong><br><em>');

        $content = str_replace('<a href="http', '<a target="_blank" rel="noreferrer nofollow" href="http', $content);
        $content = str_replace('<a href="//', '<a target="_blank" rel="noreferrer nofollow" href="http', $content);

        return $content;
    }
}
