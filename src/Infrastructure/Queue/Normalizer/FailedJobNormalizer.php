<?php

namespace App\Infrastructure\Queue\Normalizer;

use App\Http\Encoder\PathEncoder;
use App\Infrastructure\Queue\FailedJob;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FailedJobNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof FailedJob) {
            return [
                'path' => 'admin_home',
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return ($data instanceof FailedJob)
            && PathEncoder::FORMAT === $format;
    }
}
