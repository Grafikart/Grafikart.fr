<?php

namespace App\Infrastructure\Search\Normalizer;

use App\Domain\Forum\Entity\Topic;
use App\Normalizer\Normalizer;

class TopicNormalizer extends Normalizer
{
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Topic && 'search' === $format;
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof Topic) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got '.$object::class);
        }

        return [
            'id' => (string) $object->getId(),
            'title' => $object->getName(),
            'category' => $object->getTags()->map(fn ($t) => $t->getName())->toArray(),
            'content' => $object->getContent(),
            'type' => 'topic',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Topic::class => true,
        ];
    }
}
