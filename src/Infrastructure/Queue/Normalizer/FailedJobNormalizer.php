<?php

namespace App\Infrastructure\Queue\Normalizer;

use App\Http\Encoder\PathEncoder;
use App\Infrastructure\Queue\FailedJob;
use App\Normalizer\Normalizer;

class FailedJobNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof FailedJob) {
            return [
                'path' => 'admin_home',
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof FailedJob)
            && PathEncoder::FORMAT === $format;
    }
}
