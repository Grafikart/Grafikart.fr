<?php

namespace App\Normalizer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class Normalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    abstract public function normalize($object, string $format = null, array $context = []): array;

    abstract public function supportsNormalization($data, string $format = null): bool;

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
