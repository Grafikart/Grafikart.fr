<?php

namespace App\Http\Cms\Data\Technology;

use App\Concerns\Media\MapMedia;
use App\Http\Cms\Data\OptionItemData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TechnologyFormData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $slug = '',
        public string $content = '',
        #[MapMedia()]
        public ?string $image = null,
        /** @var Collection<OptionItemData> */
        public ?Collection $requirements = null,
    ) {
        $this->requirements ??= collect();
    }
}
