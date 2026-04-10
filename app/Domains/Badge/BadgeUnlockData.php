<?php

namespace App\Domains\Badge;

readonly class BadgeUnlockData
{
    public function __construct(
        public string $name,
        public string $description,
        public string $theme,
        public ?string $image,
        public bool $unlocked,
    ) {}
}
