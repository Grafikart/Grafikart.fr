<?php

namespace App\Http\Cms\Data\Support;

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
        public int $timestamp = 0,
        public \DateTimeInterface $createdAt = new \DateTimeImmutable,
    ) {}

}
