<?php

namespace App\Http\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Forum\Entity\Tag;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ForumTagNormalizer implements NormalizerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
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

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Tag && 'json' === $format;
    }
}
