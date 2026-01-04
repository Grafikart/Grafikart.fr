<?php

namespace App\Component\ObjectMapper\Transform;

use App\Component\ObjectMapper\TransformCallableInterface;

final readonly class EntityIdTransformer implements TransformCallableInterface
{

    public function __invoke(mixed $value, object $source, ?object $target): int
    {
        return $value->getId();
    }
}
