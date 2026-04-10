<?php

namespace App\Http\Cms\Data\Badge;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BadgeRowData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $action,
        #[MapInputName('action_count')]
        public int $actionCount,
    ) {}
}
