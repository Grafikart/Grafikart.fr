<?php

namespace App\Http\Cms\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OptionItemData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}
}
