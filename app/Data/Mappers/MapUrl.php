<?php

namespace App\Data\Mappers;

use App\Helpers\UrlGenerator;
use Spatie\LaravelData\Attributes\InjectsPropertyValue;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class MapUrl implements InjectsPropertyValue
{
    public function resolve(
        DataProperty $dataProperty,
        mixed $payload,
        array $properties,
        CreationContext $creationContext
    ): string {
        return app(UrlGenerator::class)->url($payload);
    }

    public function shouldBeReplacedWhenPresentInPayload(): bool
    {
        return true;
    }
}
