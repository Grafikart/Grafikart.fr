<?php

namespace App\Http\Front\Data;

use App\Domains\Course\Formation;
use App\Domains\History\Progress;
use Spatie\LaravelData\Data;

class StudentProgressData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $icon,
        public readonly int $chapters,
        public readonly int $completedChapters,
        public readonly ?string $url = null,
    ) {}

    public static function fromModel(Progress $progress): self
    {
        $formation = $progress->progressable;
        assert($formation instanceof Formation);
        $chaptersCount = $formation->course_ids->count();

        return new self(
            title: $formation->title,
            icon: $formation->icon(),
            chapters: $chaptersCount,
            completedChapters: round($chaptersCount * $progress->progress / 1000),
            url: app_url($formation),
        );
    }
}
