<?php

namespace App\Http\Cms\Data\Support;

use App\Domains\Support\SupportQuestion;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class SupportQuestionFormData extends Data
{
    public function __construct(
        public int $id,
        public string $title = '',
        public string $content = '',
        public string $answer = '',
        public bool $online = false,
        public int $courseId = 0,
        public string $courseTitle = '',
        public string $courseUrl = '',
        public int $timestamp = 0,
        public \DateTimeInterface $createdAt = new \DateTimeImmutable,
    ) {}

    public static function fromModel(SupportQuestion $model): static
    {
        return new self(
            id: $model->id,
            title: $model->title,
            content: $model->content ?? '',
            answer: $model->answer ?? '',
            online: $model->online,
            courseId: $model->course_id,
            courseTitle: $model->course->title,
            courseUrl: app_url($model->course),
            timestamp: $model->timestamp,
            createdAt: $model->created_at ?? new \DateTimeImmutable,
        );
    }
}
