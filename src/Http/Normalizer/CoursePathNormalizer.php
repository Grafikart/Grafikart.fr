<?php

namespace App\Http\Normalizer;

use App\Domain\Course\Entity\Course;
use App\Http\Encoder\PathEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CoursePathNormalizer implements NormalizerInterface
{

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof Course) {
            return [
                'path' => 'course_show',
                'params' => ['slug' => $object->getSlug(), 'id' => $object->getId()]
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return ($data instanceof Course)
            && $format === PathEncoder::FORMAT;
    }
}
