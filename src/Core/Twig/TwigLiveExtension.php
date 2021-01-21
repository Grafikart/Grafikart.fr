<?php

namespace App\Core\Twig;

use App\Domain\Live\LiveService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigLiveExtension extends AbstractExtension
{
    private LiveService $liveService;
    private ?\DateTimeImmutable $liveAt = null;

    public function __construct(LiveService $liveService)
    {
        $this->liveService = $liveService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'is_live_running',
                [$this->liveService, 'isLiveRunning'],
            ),
            new TwigFunction(
                'next_live_time',
                [$this, 'getNextLiveTime'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getNextLiveTime(): string
    {
        $liveDate = $this->liveService->getNextLiveDate();
        // Le live est passé
        if ($liveDate->modify('+2 hour') < new \DateTime()) {
            return '';
        }
        // Le live est en cours
        if ($liveDate < new \DateTime()) {
            return "<small class='text-muted'>(En cours)</small>";
        }
        // Le live est dans le futur
        $diff = $liveDate->diff(new \DateTime());
        if ((int) $diff->format('%d') > 0) {
            return "<small class='text-muted'>(J-{$diff->d})</small>";
        } elseif ((int) $diff->format('%h') > 0) {
            return "<small class='text-muted'>(H-{$diff->h})</small>";
        } elseif ((int) $diff->format('%i') > 0) {
            return "<small class='text-muted'>({$diff->format('%i')} min)</small>";
        }

        return "<small class='text-muted'>(bientôt)</small>";
    }
}
