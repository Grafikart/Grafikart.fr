<?php

namespace App\Twig;

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
            new TwigFunction('menu_active', [$this, 'menuActive'], ['is_safe' => ['html'], 'needs_context' => true])
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt']),
            new TwigFilter('markdown', [$this, 'markdown'])
        ];
    }

    /**
     * Génère le code HTML pour une icone SVG
     */
    public function svgIcon(string $name): string
    {
        return <<<HTML
        <svg class="icon">
          <use xlink:href="/sprite.svg#{$name}"></use>
        </svg>
        HTML;
    }

    /**
     * Ajout une class is-active pour les éléments actifs du menu
     * @param array<string,mixed> $context
     */
    public function menuActive(array $context, string $name): string
    {
        if (($context['menu'] ?? null) === $name) {
            return " aria-current=\"page\"";
        }
        return '';
    }

    /**
     * Renvoie un extrait d'un texte
     */
    public function excerpt(?string $content, int $characterLimit = 135): string
    {
        if ($content === null) {
            return '';
        }
        if (mb_strlen($content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos($content, ' ', $characterLimit);
        if ($lastSpace === false) {
            return $content;
        }
        return substr($content, 0, $lastSpace) . '...';
    }

    /**
     * Convertit le contenu markdown en HTML
     */
    public function markdown(?string $content): string
    {
        if ($content === null) {
            return '';
        }
        return (new Parsedown())->text($content);
    }
}
