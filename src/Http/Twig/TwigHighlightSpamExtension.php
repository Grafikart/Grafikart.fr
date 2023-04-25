<?php

namespace App\Http\Twig;

use App\Infrastructure\Spam\SpamService;
use Twig\TwigFilter;

class TwigHighlightSpamExtension extends TwigHighlightExtension
{
    /** @var string[]|null */
    private ?array $spamWords = null;

    public function __construct(readonly private SpamService $spamService)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'highlight_spamwords',
                $this->highlightSpamWords(...),
                ['pre_escape' => 'html', 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Met en surbrillance les mots potentiellement spam dans une chaîne de caractère.
     */
    public function highlightSpamWords(string $content): string
    {
        $spamWords = $this->getSpamWords();
        $highlight = array_map(fn ($word) => '<mark>'.$word.'</mark>', $spamWords);

        return nl2br(str_replace($spamWords, $highlight, $content));
    }

    /**
     * @return string[]
     */
    public function getSpamWords(): array
    {
        if (!$this->spamWords) {
            $this->spamWords = $this->spamService->words();
        }
        return $this->spamWords;
    }
}
