<?php

namespace App\Http\Twig;

use App\Infrastructure\Spam\SpamService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigSpamExtension extends AbstractExtension
{
    public function __construct(private readonly SpamService $spamService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_spam', $this->countSpam(...)),
        ];
    }

    public function countSpam(): int
    {
        return $this->spamService->count();
    }
}
