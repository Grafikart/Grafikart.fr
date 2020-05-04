<?php

namespace App\Http\Normalizer;

use App\Domain\Course\Entity\Technology;
use App\Http\Encoder\PathEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TechnologyPathNormalizer implements NormalizerInterface
{

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof Technology) {
            return [
                'path' => 'technology_show',
                'params' => ['slug' => $object->getSlug()]
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return ($data instanceof Technology)
            && $format === PathEncoder::FORMAT;
    }
}
