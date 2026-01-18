<?php

namespace App\Concerns\Media;

use Spatie\LaravelData\Attributes\InjectsPropertyValue;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

/**
 * Resolve the media URL linked to the annotated property
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class MapMedia implements InjectsPropertyValue
{
    public function __construct(
        public ?string $property = null
    ) {}

    public function resolve(DataProperty $dataProperty, mixed $payload, array $properties, CreationContext $creationContext): mixed
    {
        return $payload->mediaUrl($this->property ?? $dataProperty->name);
    }

    public function shouldBeReplacedWhenPresentInPayload(): bool
    {
        return true;
    }
}
