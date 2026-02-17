<?php

namespace App\Http\Cms\Data\Question;

use App\Domains\Evaluation\Question;
use App\Domains\Evaluation\QuestionType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionData extends Data
{
    public function __construct(
        public int $id,
        public string $question,
        public QuestionType $type,
        public QuestionChoicesAnswer|QuestionTextAnswer $answer,
    ) {}

    public static function fromModel(Question $question): self
    {
        $answer = match ($question->type) {
            QuestionType::Choice => QuestionChoicesAnswer::from($question->answer),
            QuestionType::Text => QuestionTextAnswer::from($question->answer)
        };

        return new self(
            id: $question->id,
            question: $question->question,
            type: $question->type,
            answer: $answer,
        );
    }
}
