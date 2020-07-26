<?php

namespace App\Infrastructure\Search\Normalizer;

use App\Domain\Course\Entity\Course;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class CourseNormalizer implements ContextAwareNormalizerInterface
{
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Course && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Course) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Course, got '.get_class($object));
        }
        $title = $object->getTitle();
        $formation = $object->getFormation();
        if (null !== $formation) {
            $title = $formation->getTitle().' : '.$title;
        }

        return [
            'id' => (string) $object->getId(),
            'content' => $object->getContent(),
            'title' => $title,
            'category' => array_map(fn ($t) => $t->getName(), $object->getMainTechnologies()),
            'type' => 'course',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
