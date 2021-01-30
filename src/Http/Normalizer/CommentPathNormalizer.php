<?php

namespace App\Http\Normalizer;

use App\Domain\Blog\Post;
use App\Domain\Comment\Comment;
use App\Domain\Course\Entity\Course;
use App\Http\Encoder\PathEncoder;
use App\Normalizer\Normalizer;

class CommentPathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Comment) {
            $target = $object->getTarget();
            if ($target instanceof Post) {
                $path = (new PostPathNormalizer())->normalize($target, $format, $context);
            }
            if ($target instanceof Course) {
                $path = (new CoursePathNormalizer())->normalize($target, $format, $context);
            }
            $path['hash'] = "c{$object->getId()}";

            return $path;
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Comment)
            && PathEncoder::FORMAT === $format;
    }
}
