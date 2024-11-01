<?php

namespace App\Http\Normalizer;

use App\Domain\Course\Entity\Technology;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class TechnologyPathNormalizer extends Normalizer
{
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        if ($object instanceof Technology) {
            return [
                'path' => 'technology_show',
                'params' => ['slug' => $object->getSlug()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return ($data instanceof Technology)
            && PathEncoder::FORMAT === $format;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Technology::class => true,
        ];
    }
}
