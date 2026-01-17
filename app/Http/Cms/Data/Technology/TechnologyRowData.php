<?php

namespace App\Http\Cms\Data\Technology;

use App\Concerns\Media\MapMedia;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TechnologyRowData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        #[MapMedia()]
        public ?string $image,
        public int $tutorialCount = 0,
    ) {}

}
