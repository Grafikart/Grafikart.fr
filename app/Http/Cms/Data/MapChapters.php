<?php

namespace App\Http\Cms\Data;

use App\Domains\Course\Chapter;
use App\Domains\Course\Course;
use App\Http\Cms\Data\Course\ChapterData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\InjectsPropertyValue;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

/**
 * Resolve the media URL linked to the annotated property
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class MapChapters implements InjectsPropertyValue
{
    public function __construct(
        public ?string $property = null
    ) {}

    /**
     * @return array<ChapterData>
     */
    public function resolve(DataProperty $dataProperty, mixed $payload, array $properties, CreationContext $creationContext): array
    {
        /** @var Collection<Chapter> $chapters */
        $chapters = collect($payload[$dataProperty->name]);
        $ids = $chapters->pluck('ids')->flatten();
        $courses = Course::query()
            ->whereIn('id', $ids)
            ->select('title', 'id')
            ->get()
            ->keyBy('id');

        return $chapters->map(fn (Chapter $chapter) => new ChapterData(
            title: $chapter->title,
            courses: array_map(function (int $id) use ($courses) {
                $course = $courses[$id];

                return new OptionItemData(
                    id: $course->id,
                    name: $course->title,
                );
            }, $chapter->ids)
        ))->all();
    }

    public function shouldBeReplacedWhenPresentInPayload(): bool
    {
        return true;
    }
}
