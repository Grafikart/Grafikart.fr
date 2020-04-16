<?php

namespace App\Infrastructure\Search\Normalizer;

use App\Domain\Course\Entity\Formation;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class FormationNormalizer implements ContextAwareNormalizerInterface
{

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Formation && $format === 'search';
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Formation) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got ' . get_class($object));
        }
        return [
            'id' => (string)$object->getId(),
            'content' => $object->getContent(),
            'title' => $object->getTitle(),
            'category' => array_map(fn($t) => $t->getName(), $object->getMainTechnologies()),
            'type' => 'formation',
            'created_at' => $object->getCreatedAt()->getTimestamp()
        ];
    }
}
