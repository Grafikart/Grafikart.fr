<?php

namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Course\Entity\Formation;
use App\Http\Normalizer\FormationPathNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class FormationNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(private readonly FormationPathNormalizer $pathNormalizer, private readonly UrlGeneratorInterface $urlGenerator)
    {
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
            'content' => $object->getContent(),
            'title' => $object->getTitle(),
            'url' => $this->urlGenerator->generate($url['path'], $url['params']),
            'category' => array_map(fn ($t) => $t->getName(), $object->getMainTechnologies()),
            'type' => 'formation',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
