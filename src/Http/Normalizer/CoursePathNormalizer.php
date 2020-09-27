<?php

namespace App\Http\Normalizer;

use App\Core\Normalizer;
use App\Domain\Course\Entity\Course;
use App\Http\Encoder\PathEncoder;

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
