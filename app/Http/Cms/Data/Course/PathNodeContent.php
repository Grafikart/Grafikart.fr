<?php

namespace App\Http\Cms\Data\Course;

use Spatie\LaravelData\Data;

class PathNodeContent extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
    ) {}

}
