<?php

namespace App\Domain\Course\DTO;

use App\Domain\Course\Entity\TechnologyUsage;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final readonly class ContentTechnologyDTO
{
    public function __construct(
        public int $id,
        public bool $primary = false,
        public ?string $version = null,
        public string $name = '',
    ) {
    }

    public static function fromUsage(TechnologyUsage $usage): self
    {
        return new self(
            id: $usage->getTechnology()->getId(),
            primary: !$usage->getSecondary(),
            version: $usage->getVersion(),
            name: $usage->getTechnology()->getName()
        );
    }
}
