<?php

namespace App\Infrastructure\Search\Normalizer;

use App\Domain\Forum\Entity\Topic;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class TopicNormalizer implements ContextAwareNormalizerInterface
{

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Topic && $format === 'search';
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Topic) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got ' . get_class($object));
        }
        return [
            'id' => (string)$object->getId(),
            'content' => $object->getContent(),
            'title' => $object->getName(),
            'category' => $object->getTags()->map(fn($t) => $t->getName())->toArray(),
            'type' => 'topic',
            'created_at' => $object->getCreatedAt()->getTimestamp()
        ];
    }
}
