<?php

namespace App\Http\Normalizer;

use App\Domain\Auth\User;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class UserPathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof User) {
            return [
                'path' => 'user_show',
                'params' => ['id' => $object->getId()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof User && PathEncoder::FORMAT === $format;
    }
}
