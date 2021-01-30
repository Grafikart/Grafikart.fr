<?php

namespace App\Http\Normalizer;

use App\Domain\Course\Entity\Course;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class CoursePathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Course) {
            return [
                'path' => 'course_show',
                'params' => ['slug' => $object->getSlug(), 'id' => $object->getId()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Course)
            && PathEncoder::FORMAT === $format;
    }
}
