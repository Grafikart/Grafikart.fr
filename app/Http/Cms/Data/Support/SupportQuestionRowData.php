<?php

namespace App\Http\Cms\Data\Support;

use App\Domains\Support\SupportQuestion;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class SupportQuestionRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $courseTitle,
        public readonly bool $online,
        public readonly bool $answered,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    public static function fromModel(SupportQuestion $supportQuestion): self
    {
        return new self(
            id: $supportQuestion->id,
            title: $supportQuestion->title,
            courseTitle: $supportQuestion->course->title,
            online: $supportQuestion->online,
            answered: filled(trim((string) $supportQuestion->answer)),
            createdAt: $supportQuestion->created_at,
        );
    }
}
