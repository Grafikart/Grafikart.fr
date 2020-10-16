<?php

namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Blog\Post;
use App\Http\Normalizer\PostPathNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class PostNormalizer implements ContextAwareNormalizerInterface
{
    private PostPathNormalizer $pathNormalizer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(PostPathNormalizer $pathNormalizer, UrlGeneratorInterface $urlGenerator)
    {
        $this->pathNormalizer = $pathNormalizer;
        $this->urlGenerator = $urlGenerator;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Post && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Post) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Course, got '.get_class($object));
        }
        $title = $object->getTitle();
        $url = $this->pathNormalizer->normalize($object);

        return [
            'id' => (string) $object->getId(),
            'content' => MarkdownTransformer::toText((string) $object->getContent()),
            'url' => $this->urlGenerator->generate($url['path'], $url['params']),
            'title' => $title,
            'category' => [],
            'type' => 'post',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
