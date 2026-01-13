<?php

namespace App\Http\Admin\Data\Technology;

use App\Component\ObjectMapper\Attribute\MapCollection;
use App\Http\Data\OptionItemData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class TechnologyFormData
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $slug = '',
        public string $content = '',
        public ?string $image = null,
        #[MapCollection(item: OptionItemData::class, source: 'requirements')]
        /** @var OptionItemData[] */
        public array $requirements = [],
    ) {
    }
}
