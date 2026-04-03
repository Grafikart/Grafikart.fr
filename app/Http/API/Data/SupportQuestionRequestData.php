<?php

namespace App\Http\API\Data;

use App\Domains\Course\Course;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SupportQuestionRequestData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $content,
        public readonly int $timestamp,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        /** @var Course $course */
        $course = request()->route('course');

        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'content' => ['nullable', 'string'],
            'timestamp' => ['required', 'integer', 'min:0', 'max:'.$course->duration],
        ];
    }
}
