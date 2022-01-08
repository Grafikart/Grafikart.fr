<?php

namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Blog\Post;
use App\Http\Normalizer\PostPathNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class PostNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(
        private readonly PostPathNormalizer $pathNormalizer,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Post && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Post) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Course, got ' . $object::class);
        }
        $title = $object->getTitle();
        $url = $this->pathNormalizer->normalize($object);

        return [
            'id'         => (string)$object->getId(),
            'content'    => MarkdownTransformer::toText((string)$object->getContent()),
            'url'        => $this->urlGenerator->generate($url['path'], $url['params']),
            'title'      => $title,
            'category'   => [],
            'type'       => 'post',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
