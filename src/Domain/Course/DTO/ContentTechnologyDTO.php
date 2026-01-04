<?php

namespace App\Domain\Course\DTO;

use App\Component\ObjectMapper\Attribute\Map;
use App\Component\ObjectMapper\Transform\InverseTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final readonly class ContentTechnologyDTO
{

    public function __construct(
        #[Map(source: 'technology.id')]
        public int $id,
        #[Map(source: 'secondary', transform: InverseTransformer::class)]
        public bool $primary = false,
        public ?string $version = null,
        #[Map(source: 'technology.name')]
        public string $name = '',
    ) {
    }

}
