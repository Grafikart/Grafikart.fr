<?php

namespace App\Http\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Forum\Entity\Tag;
use App\Normalizer\Normalizer;

class ForumTagNormalizer extends Normalizer
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Tag) {
            return [
                'id' => $object->getId(),
                'position' => $object->getPosition(),
                'name' => $object->getName(),
                'url' => $this->urlGenerator->generate('admin_forum-tag_edit', ['id' => $object->getId()]),
                'children' => $object->getChildren()->map(function (Tag $tag) {
                    return $this->normalize($tag);
                })->toArray(),
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Tag && 'json' === $format;
    }
}
