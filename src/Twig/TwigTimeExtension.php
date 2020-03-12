<?php

namespace App\Twig;

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
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function duration(int $duration): string
    {
        $minutes = round($duration / 60);
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        $hours = floor($minutes / 60);
        $minutes = $minutes - ($hours * 60);
        return "{$hours}h{$minutes}";
    }

    public function ago (\DateTimeInterface $date): string
    {
        return "<time-ago time=\"{$date->getTimestamp()}\"></time-ago>";
    }

}
