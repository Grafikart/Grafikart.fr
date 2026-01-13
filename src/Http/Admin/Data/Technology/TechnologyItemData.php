<?php

namespace App\Http\Admin\Data\Technology;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class TechnologyItemData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $image,
        public int $tutorialCount,
    ) {
    }

}
