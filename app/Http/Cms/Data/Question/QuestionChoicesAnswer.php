<?php

namespace App\Http\Cms\Data\Question;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionChoicesAnswer extends Data
{
    public function __construct(
        /** @var string[] */
        public array $choices,
        /** @var int */
        public int $answer,
    ) {}
}
