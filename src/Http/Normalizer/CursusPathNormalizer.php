<?php

namespace App\Http\Normalizer;

use App\Domain\Course\Entity\Cursus;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class CursusPathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Cursus) {
            return [
                'path' => 'cursus_show',
                'params' => ['slug' => $object->getSlug()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Cursus)
            && PathEncoder::FORMAT === $format;
    }
}
