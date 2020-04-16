<?php

namespace App\Infrastructure\Search\Normalizer;

use App\Domain\Course\Entity\Course;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class CourseNormalizer implements ContextAwareNormalizerInterface
{

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Course && $format === 'search';
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Course) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got ' . get_class($object));
        }
        $title = $object->getTitle();
        $formation = $object->getFormation();
        if ($formation !== null) {
            $title = $formation->getTitle() . ' : '  . $title;
        }
        return [
            'id' => (string)$object->getId(),
            'content' => $object->getContent(),
            'title' => $title,
            'category' => array_map(fn($t) => $t->getName(), $object->getMainTechnologies()),
            'type' => 'course',
            'created_at' => $object->getCreatedAt()->getTimestamp()
        ];
    }
}
