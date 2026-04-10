<?php

namespace App\Http\Cms\Data\Badge;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BadgeFormData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $description = '',
        public int $position = 0,
        public string $action = '',
        public int $actionCount = 0,
        public string $theme = 'grey',
        public ?string $image = null,
        public bool $unlockable = false,
    ) {}
}
