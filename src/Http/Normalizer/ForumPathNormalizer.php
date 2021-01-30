<?php

namespace App\Http\Normalizer;

use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class ForumPathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Tag) {
            return [
                'path' => 'forum_tag',
                'params' => ['id' => $object->getId(), 'slug' => $object->getSlug()],
            ];
        } elseif ($object instanceof Topic) {
            return [
                'path' => 'forum_show',
                'params' => ['id' => $object->getId()],
            ];
        } elseif ($object instanceof Message) {
            return [
                'path' => 'forum_show',
                'params' => ['id' => $object->getTopic()->getId()],
                'hash' => 'message-'.$object->getId(),
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Tag || $data instanceof Topic || $data instanceof Message)
            && PathEncoder::FORMAT === $format;
    }
}
