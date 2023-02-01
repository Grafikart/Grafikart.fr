<?php

namespace App\Http\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigHighlightExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'highlight_words',
                $this->highlightWords(...),
                ['pre_escape' => 'html', 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Met en surbrillance les mots trouvé dans une chaîne de caractère.
     */
    public function highlightWords(string $content, array $words): string
    {
        $highlight = array_map(fn ($word) => '<mark>'.$word.'</mark>', $words);

        return nl2br(str_replace($words, $highlight, $content));
    }
}
