<?php

namespace App\Http\Normalizer;

use App\Domain\Blog\Post;
use App\Http\Encoder\PathEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PostPathNormalizer implements NormalizerInterface
{

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof Post) {
            return [
                'path' => 'blog_show',
                'params' => ['slug' => $object->getSlug()]
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return ($data instanceof Post)
            && $format === PathEncoder::FORMAT;
    }
}
