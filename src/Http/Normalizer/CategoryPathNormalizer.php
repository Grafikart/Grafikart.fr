<?php

namespace App\Http\Normalizer;

use App\Core\Normalizer;
use App\Domain\Blog\Category;
use App\Http\Encoder\PathEncoder;

class CategoryPathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Category) {
            return [
                'path' => 'blog_category',
                'params' => ['slug' => $object->getSlug()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Category)
            && PathEncoder::FORMAT === $format;
    }
}
