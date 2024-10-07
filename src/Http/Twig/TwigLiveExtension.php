<?php

namespace App\Http\Twig;

use App\Domain\Live\LiveService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigLiveExtension extends AbstractExtension
{
    public function __construct(private readonly LiveService $liveService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'is_live_running',
                $this->liveService->isLiveRunning(...),
            ),
            new TwigFunction(
                'next_live_time',
                $this->getNextLiveTime(...),
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'is_live_available',
                $this->getLiveAvailable(...),
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getLiveAvailable(): bool
    {
        $liveDate = $this->liveService->getNextLiveDate();

        return $liveDate->modify('+5 hour') > new \DateTimeImmutable();
    }

    public function getNextLiveTime(): string
    {
        $liveDate = $this->liveService->getNextLiveDate();
        // Le live est passé
        if ($liveDate->modify('+2 hour') < new \DateTimeImmutable()) {
            return '';
        }
        // Le live est en cours
        if ($liveDate < new \DateTimeImmutable()) {
            return "<small class='text-muted'>(En cours)</small>";
        }
        // Le live est dans le futur
        $diff = $liveDate->getTimestamp() - time();
        if ($diff > 24 * 3600) {
            $days = ceil($diff / (24 * 3600));

            return "<small class='text-muted'>(J-{$days})</small>";
        }
        $date = $liveDate->format('H:i');

        return "<small class='text-muted'>({$date})</small>";
    }
}
