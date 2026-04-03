<?php

namespace App\Http\API\Data;

use App\Domains\Support\SupportQuestion;
use App\Helpers\MarkdownHelper;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SupportQuestionData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly ?string $answer,
        public readonly int $timestamp,
        public readonly string $createdAt,
        public readonly bool $me,
    ) {}

    public static function fromModel(SupportQuestion $question): self
    {
        return new self(
            id: $question->id,
            title: $question->title,
            content: $question->content,
            answer: MarkdownHelper::html($question->answer),
            timestamp: $question->timestamp,
            createdAt: $question->created_at->toIso8601String(),
            me: Auth::user()?->id === $question->user_id,
        );
    }
}
