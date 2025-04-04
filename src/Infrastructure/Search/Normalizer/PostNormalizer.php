<?php

namespace App\Infrastructure\Search\Normalizer;

use App\Domain\Blog\Post;
use App\Http\Normalizer\PostPathNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PostNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly PostPathNormalizer $pathNormalizer,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Post && 'search' === $format;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof Post) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Course, got '.$object::class);
        }
        $title = $object->getTitle();
        $url = $this->pathNormalizer->normalize($object);

        return [
            'id' => (string) $object->getId(),
            'title' => $title,
            'category' => [],
            'content' => MarkdownTransformer::toText((string) $object->getContent()),
            'url' => $this->urlGenerator->generate($url['path'], $url['params']),
            'type' => 'post',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Post::class => true,
        ];
    }
}
