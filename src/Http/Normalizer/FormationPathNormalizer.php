<?php

namespace App\Http\Normalizer;

use App\Domain\Course\Entity\Formation;
use App\Http\Encoder\PathEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormationPathNormalizer implements NormalizerInterface
{

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof Formation) {
            return [
                'path' => 'formation_show',
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
        return ($data instanceof Formation)
            && $format === PathEncoder::FORMAT;
    }
}
