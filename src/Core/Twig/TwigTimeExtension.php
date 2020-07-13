<?php

namespace App\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigTimeExtension extends AbstractExtension
{
    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('duration', [$this, 'duration']),
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']]),
            new TwigFilter('duration_short', [$this, 'shortDuration'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Génère une durée au format "30 min".
     */
    public function duration(int $duration): string
    {
        $minutes = round($duration / 60);
        if ($minutes < 60) {
            return $minutes.' min';
        }
        $hours = floor($minutes / 60);
        $minutes = str_pad((string) ($minutes - ($hours * 60)), 2, '0', STR_PAD_LEFT);

        return "{$hours}h{$minutes}";
    }

    /**
     * Génère une durée au format court hh:mm:ss.
     */
    public function shortDuration(int $duration): string
    {
        $minutes = floor($duration / 60);
        $seconds = $duration - $minutes * 60;
        /** @var int[] $times */
        $times = [$minutes, $seconds];
        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $minutes = $minutes - ($hours * 60);
            $times = [$hours, $minutes, $seconds];
        }

        return implode(':', array_map(
            fn (int $duration) => str_pad(strval($duration), 2, '0', STR_PAD_LEFT),
            $times
        ));
    }

    /**
     * Génère une date au format "Il y a" gràce à un CustomElement.
     */
    public function ago(\DateTimeInterface $date, string $prefix = ''): string
    {
        return "<time-ago time=\"{$date->getTimestamp()}\" prefix=\"{$prefix}\"></time-ago>";
    }
}
