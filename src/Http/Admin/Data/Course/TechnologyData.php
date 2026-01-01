<?php

namespace App\Http\Admin\Data\Course;

use App\Domain\Course\Entity\TechnologyUsage;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final readonly class TechnologyData
{

    public function __construct(
        public int $id,
        public bool $secondary = false,
        public ?string $version = null,
        public string $name = ''
    ){

    }

    public static function fromUsage(TechnologyUsage $usage): self {
        return new self(
            id: $usage->getTechnology()->getId(),
            secondary: $usage->getSecondary(),
            version: $usage->getVersion(),
            name: $usage->getTechnology()->getName()
        );
    }

}
