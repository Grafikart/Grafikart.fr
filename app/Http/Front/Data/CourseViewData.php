<?php

namespace App\Http\Front\Data;

use App\Domains\Course\Course;
use App\Helpers\MarkdownHelper;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CourseViewData extends Data
{
    public string $type = 'course';

    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $video,
        public readonly string $content,
        public readonly string $poster,
    ) {}

    public static function fromModel(Course $course, ?User $user): self
    {
        return new self(
            id: $course->id,
            title: $course->title,
            video: $course->videoSrc($user?->html5_player) ?? '',
            content: MarkdownHelper::html($course->content),
            poster: $course->posterUrl(1330, 750),
        );
    }
}
