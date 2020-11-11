<?php

namespace App\Core\Twig;

use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'svgIcon'], ['is_safe' => ['html']]),
            new TwigFunction('menu_active', [$this, 'menuActive'], ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt']),
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
            new TwigFilter('markdown_excerpt', [$this, 'markdownExcerpt'], ['is_safe' => ['html']]),
            new TwigFilter('markdown_untrusted', [$this, 'markdownUntrusted'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Génère le code HTML pour une icone SVG.
     */
    public function svgIcon(string $name, ?int $size = null): string
    {
        $attrs = '';
        if ($size) {
            $attrs = " width=\"{$size}px\" height=\"{$size}px\"";
        }

        return <<<HTML
        <svg class="icon icon-{$name}"$attrs>
          <use xlink:href="/sprite.svg#{$name}"></use>
        </svg>
        HTML;
    }

    /**
     * Ajout une class is-active pour les éléments actifs du menu.
     *
     * @param array<string,mixed> $context
     */
    public function menuActive(array $context, string $name): string
    {
        if (($context['menu'] ?? null) === $name) {
            return ' aria-current="page"';
        }

        return '';
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
        $content = (new Parsedown())->setBreaksEnabled(true)->setSafeMode(false)->text($content);
        // On remplace les liens youtube par un embed
        $content = preg_replace(
            '/<p><a href\="(http|https):\/\/www.youtube.com\/watch\?v=([^\""]+)">[^<]*<\/a><\/p>/',
            '<div class="video"><div class="ratio"><iframe width="560" height="315" src="//www.youtube-nocookie.com/embed/$2" frameborder="0" allowfullscreen=""></iframe></div></div>',
            $content
        );
        // On ajoute des liens sur les nombres représentant un timestamp "00:01"
        $content = preg_replace_callback('/((\d{2}:){1,2}\d{2}) ([^<]*)/', function ($matches) {
            $times = array_reverse(explode(':', $matches[1]));
            $title = $matches[3];
            $timecode = (int) ($times[2] ?? 0) * 60 * 60 + (int) $times[1] * 60 + (int) $times[0];

            return "<a href=\"#t{$timecode}\">{$matches[1]}</a> $title";
        }, $content) ?: $content;

        return $content;
    }

    public function markdownExcerpt(?string $content, int $characterLimit = 135): string
    {
        return $this->excerpt(strip_tags($this->markdown($content)), $characterLimit);
    }

    public function markdownUntrusted(?string $content): string
    {
        return (new Parsedown())->setSafeMode(true)->text($content);
    }
}
