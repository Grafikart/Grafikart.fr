<?php

namespace App\Http\Cms\Data\Course;

use App\Domains\Course\Course;
use App\Http\Cms\Data\OptionItemData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class ChapterData
{
    public function __construct(
        public string $title,
        /** @var OptionItemData[] */
        public array  $courses,
    ) {}

}
