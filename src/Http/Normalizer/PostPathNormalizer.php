<?php

namespace App\Http\Normalizer;

use App\Domain\Blog\Post;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class PostPathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Post) {
            return [
                'path' => 'blog_show',
                'params' => ['slug' => $object->getSlug()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Post)
            && PathEncoder::FORMAT === $format;
    }
}
