<?php

namespace App\Http\Cms\Data\Technology;

use App\Domains\Course\Models\Technology;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TechnologyRowData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $image,
        public int $tutorialCount,
    ) {}

    public static function fromModel(Technology $technology): self
    {
        return new self(
            id: $technology->id,
            name: $technology->name,
            image: $technology->image,
            tutorialCount: 0, // TODO: Add relationship count when courses are implemented
        );
    }
}
