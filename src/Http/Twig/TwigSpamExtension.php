<?php

namespace App\Http\Twig;

use App\Infrastructure\Spam\SpamService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigSpamExtension extends AbstractExtension
{
    private SpamService $spamService;

    public function __construct(
        SpamService $spamService
    ) {
        $this->spamService = $spamService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_spam', [$this, 'countSpam']),
        ];
    }

    public function countSpam(): int
    {
        return $this->spamService->count();
    }
}
