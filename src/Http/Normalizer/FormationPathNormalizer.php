<?php

namespace App\Http\Normalizer;

use App\Core\Normalizer;
use App\Domain\Course\Entity\Formation;
use App\Http\Encoder\PathEncoder;

class FormationPathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Formation) {
            return [
                'path' => 'formation_show',
                'params' => ['slug' => $object->getSlug()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Formation)
            && PathEncoder::FORMAT === $format;
    }
}
