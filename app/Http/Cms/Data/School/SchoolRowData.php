<?php

namespace App\Http\Cms\Data\School;

use App\Domains\School\School;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class SchoolRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}

    public static function fromModel(School $school): self
    {
        return new self(
            id: $school->id,
            name: $school->name,
        );
    }
}
