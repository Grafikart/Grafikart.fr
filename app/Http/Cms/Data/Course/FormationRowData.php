<?php

namespace App\Http\Cms\Data\Course;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FormationRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly bool $online,
        public readonly \DateTimeImmutable $createdAt,
        /** @var Collection<TechnologyUsageData> */
        public readonly Collection $technologies,
    ) {}
}
