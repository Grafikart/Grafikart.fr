<?php

namespace App\Component\ObjectMapper\Transform;

use App\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final class TimestampTransformer implements TransformCallableInterface
{
    public function __invoke(mixed $value, object $source, ?object $target): int
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }
        throw new \RuntimeException('Expected a DateTimeInterface for Timestamp transformation');
    }
}
