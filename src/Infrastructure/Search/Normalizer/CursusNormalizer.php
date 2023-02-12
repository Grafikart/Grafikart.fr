<?php

namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Api\UrlGeneratorInterface;
use App\Domain\Course\Entity\Cursus;
use App\Http\Normalizer\CursusPathNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CursusNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly CursusPathNormalizer $pathNormalizer,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Cursus && 'search' === $format;
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Cursus) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got '.$object::class);
        }

        $url = $this->pathNormalizer->normalize($object);

        return [
            'id' => (string) $object->getId(),
            'title' => $object->getTitle(),
            'category' => array_map(fn ($t) => $t->getName(), $object->getMainTechnologies()),
            'content' => $object->getContent(),
            'url' => $this->urlGenerator->generate($url['path'], $url['params']),
            'type' => 'cursus',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
