<?php

namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Course\Entity\Formation;
use App\Http\Normalizer\FormationPathNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class FormationNormalizer implements ContextAwareNormalizerInterface
{
    private FormationPathNormalizer $pathNormalizer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(FormationPathNormalizer $pathNormalizer, UrlGeneratorInterface $urlGenerator)
    {
        $this->pathNormalizer = $pathNormalizer;
        $this->urlGenerator = $urlGenerator;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Formation && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Formation) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Formation, got '.get_class($object));
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
