<?php

namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Api\UrlGeneratorInterface;
use App\Domain\Course\Entity\Formation;
use App\Http\Normalizer\FormationPathNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormationNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly FormationPathNormalizer $pathNormalizer,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Formation && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Formation) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got '.$object::class);
        }

        $url = $this->pathNormalizer->normalize($object);

        return [
            'id' => (string) $object->getId(),
            'title' => $object->getTitle(),
            'category' => array_map(fn ($t) => $t->getName(), $object->getMainTechnologies()),
            'content' => $object->getContent(),
            'url' => $this->urlGenerator->generate($url['path'], $url['params']),
            'type' => 'formation',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
