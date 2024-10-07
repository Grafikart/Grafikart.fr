<?php

namespace App\Http\Normalizer;

use App\Domain\Revision\Revision;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class RevisionPathNormalizer extends Normalizer
{
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        if ($object instanceof Revision) {
            return [
                'path' => 'revisions',
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Revision && PathEncoder::FORMAT === $format;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Revision::class => true,
        ];
    }
}
