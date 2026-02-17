<?php

namespace App\Http\Cms\Data\Question;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class QuestionImportData extends Data
{
    public function __construct(
        #[Required]
        #[Min(1)]
        /** @var array<QuestionRequestData> */
        public readonly array $questions,
    ) {}
}
