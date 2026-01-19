<?php

namespace App\Domains\Course;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class Chapter extends Data
{
    public function __construct(
        public string $title,
        /** @var int[] */
        public array $ids,
    ) {}
}
