<?php

namespace App\Http\API\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CountryData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
    ) {}
}
