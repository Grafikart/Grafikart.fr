<?php

namespace App\Domains\Sponsorship;

enum SponsorType: string
{
    case Affiliation = 'affiliation';
    case Sponsor = 'sponsor';

    public function label(): string
    {
        return match ($this) {
            self::Affiliation => 'Affiliation',
            self::Sponsor => 'Sponsor',
        };
    }
}
