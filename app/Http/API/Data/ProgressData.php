<?php

namespace App\Http\API\Data;

use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

class ProgressData extends Data
{
    public function __construct(
        #[Nullable, IntegerType, Between(0, 1000)]
        public ?int $progress = null,
        #[Nullable, IntegerType, Between(0, 100)]
        public ?int $score = null,
    ) {}
}
