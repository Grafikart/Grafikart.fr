<?php

namespace App\Http\Admin\Data\Technology;

use App\Component\ObjectMapper\Attribute\Map;
use App\Domain\Course\Transform\TechnologyRequirementsTransform;
use App\Validator\Slug;
use Symfony\Component\Validator\Constraints\NotBlank;

readonly class TechnologyFormInput
{
    public function __construct(
        #[Map]
        #[NotBlank]
        public string $name,
        #[Map]
        #[NotBlank]
        #[Slug]
        public string $slug,
        #[Map]
        public ?string $content,
        /** @var int[] */
        #[Map(transform: TechnologyRequirementsTransform::class)]
        public array $requirements = [],
    ) {
    }
}
