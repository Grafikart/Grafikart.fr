<?php

namespace App\Http\API\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class APISearchItem extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $url,
        public readonly string $type,
    ) {}
}
