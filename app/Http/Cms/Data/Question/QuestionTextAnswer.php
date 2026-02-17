<?php

namespace App\Http\Cms\Data\Question;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionTextAnswer extends Data
{
    public function __construct(
        public string $answer,
    ) {}
}
