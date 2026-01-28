<?php

namespace App\Http\Front\Data;

use App\Data\Transformers\MarkdownTransformer;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FormationViewData extends Data
{
    public string $type = 'formation';

    public function __construct(
        #[WithTransformer(MarkdownTransformer::class)]
        public readonly string $content,
        #[WithTransformer(MarkdownTransformer::class)]
        public readonly ?string $links = null,
        /** @var FormationChapterData[] */
        #[MapInputName('chaptersWithCourses')]
        public readonly array $chapters = [],
        public readonly int $duration = 0,
    ) {}
}
