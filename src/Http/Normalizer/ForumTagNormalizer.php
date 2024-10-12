<?php

namespace App\Http\Normalizer;

use App\Domain\Forum\Entity\Tag;
use App\Normalizer\Normalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForumTagNormalizer extends Normalizer
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        if ($object instanceof Tag) {
            return [
                'id' => $object->getId(),
                'position' => $object->getPosition(),
                'name' => $object->getName(),
                'url' => $this->urlGenerator->generate('admin_forum-tag_edit', ['id' => $object->getId()]),
                'children' => $object->getChildren()->map(fn (Tag $tag) => $this->normalize($tag))->toArray(),
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Tag && 'json' === $format;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Tag::class => true,
        ];
    }
}
