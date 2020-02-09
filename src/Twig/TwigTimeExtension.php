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
            new TwigFilter('duration', [$this, 'duration'])
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

}
