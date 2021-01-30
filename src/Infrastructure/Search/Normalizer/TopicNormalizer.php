<?php

namespace App\Infrastructure\Search\Normalizer;

use App\Domain\Forum\Entity\Topic;
use App\Normalizer\Normalizer;

class TopicNormalizer extends Normalizer
{
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Topic && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Topic) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got '.get_class($object));
        }

        return [
            'id' => (string) $object->getId(),
            'content' => $object->getContent(),
            'title' => $object->getName(),
            'category' => $object->getTags()->map(fn ($t) => $t->getName())->toArray(),
            'type' => 'topic',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
