<?php

namespace App\Http\Front\Data;

use App\Data\Mappers\MapUrl;
use App\Data\Transformers\MarkdownTransformer;
use App\Domains\Course\Course;
use App\Helpers\MarkdownHelper;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CourseViewData extends Data
{
    public string $type = 'course';

    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly string $poster,
    ) {}

    public static function fromModel(Course $course): self
    {
        return new self(
            id: $course->id,
            title: $course->title,
            content: MarkdownHelper::html($course->content),
            poster: $course->posterUrl(1330, 750),
        );
    }
}
