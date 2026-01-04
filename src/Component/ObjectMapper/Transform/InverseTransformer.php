<?php

namespace App\Component\ObjectMapper\Transform;

use App\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final readonly class InverseTransformer implements TransformCallableInterface
{

    public function __invoke(mixed $value, object $source, ?object $target): int
    {
        return !$value;
    }
}
