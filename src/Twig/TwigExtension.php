<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
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
     */
    public function menuActive(array $context, string $name): string
    {
        if (($context['menu'] ?? null) === $name) {
            return " aria-current=\"page\"";
        }
        return '';
    }

}
