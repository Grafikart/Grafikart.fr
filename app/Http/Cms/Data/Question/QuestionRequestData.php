<?php

namespace App\Http\Cms\Data\Question;

use App\Domains\Evaluation\QuestionType;
use Spatie\LaravelData\Data;

class QuestionRequestData extends Data
{
    public function __construct(
        public readonly string $question,
        public readonly QuestionType $type,
        public readonly array $answer,
    ) {}

    public static function rules(): array
    {
        $choice = QuestionType::Choice->value;

        return [
            'question' => ['required', 'string', 'min:2'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(QuestionType::cases(), 'value'))],
            'answer' => ['required', 'array'],
            // Choice: choices list + correct answer index
            'answer.choices' => ["exclude_unless:type,{$choice}", 'required', 'array', 'min:2'],
            'answer.choices.*' => ['required', 'string'],
            'answer.answer' => ['required'],
        ];
    }
}
