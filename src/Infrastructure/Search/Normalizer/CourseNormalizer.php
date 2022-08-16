<?php

namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Course\Entity\Course;
use App\Http\Normalizer\CoursePathNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CourseNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly CoursePathNormalizer $pathNormalizer,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Course && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Course) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Course, got '.$object::class);
        }
        $title = $object->getTitle();
        $formation = $object->getFormation();
        if (null !== $formation) {
            $title = $formation->getTitle().' : '.$title;
        }

        $url = $this->pathNormalizer->normalize($object);

        return [
            'id' => (string) $object->getId(),
            'content' => MarkdownTransformer::toText((string) $object->getContent()),
            'url' => $this->urlGenerator->generate($url['path'], $url['params']),
            'title' => $title,
            'category' => array_map(fn ($t) => $t->getName(), $object->getMainTechnologies()),
            'type' => 'course',
            'created_at' => $object->getCreatedAt()->getTimestamp(),
        ];
    }
}
