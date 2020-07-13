<?php

namespace App\Core\Twig;

use App\Domain\Live\LiveService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigLiveExtension extends AbstractExtension
{
    private LiveService $liveService;

    public function __construct(LiveService $liveService)
    {
        $this->liveService = $liveService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'is_live_running',
                [$this, 'isLiveRuning'],
            ),
            new TwigFunction(
                'next_live_time',
                [$this, 'getNextLiveTime'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function isLiveRuning(): bool
    {
        $live = $this->liveService->getCurrentLive();

        return $live && $live->getCreatedAt() < new \DateTime();
    }

    public function getNextLiveTime(): string
    {
        $live = $this->liveService->getCurrentLive();
        if (null === $live) {
            return '';
        }
        if ($live->getCreatedAt() < new \DateTime()) {
            return "<small class='text-muted'>(En cours)</small>";
        }
        $diff = $live->getCreatedAt()->diff(new \DateTime());
        if ((int) $diff->format('%d') > 0) {
            return "<small class='text-muted'>(J-{$diff->d})</small>";
        } else {
            if ((int) $diff->format('%h') > 0) {
                return "<small class='text-muted'>(H-{$diff->h})</small>";
            }
        }

        return "<small class='text-muted'>(bientôt)</small>";
    }
}
