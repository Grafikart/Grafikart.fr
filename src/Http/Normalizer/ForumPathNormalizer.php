<?php

namespace App\Http\Normalizer;

use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use App\Http\Encoder\PathEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ForumPathNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
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

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return ($data instanceof Tag || $data instanceof Topic || $data instanceof Message)
            && PathEncoder::FORMAT === $format;
    }
}
