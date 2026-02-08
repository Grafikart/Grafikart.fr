<?php

namespace App\Http\API\Data;

use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class ProgressData extends Data
{
    public function __construct(
        #[Required, IntegerType, Between(0, 1000)]
        public int $progress,
    ) {}
}
