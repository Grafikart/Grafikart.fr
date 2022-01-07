<?php

namespace App\Domain\Badge\Normalizer;

use App\Domain\Badge\Entity\Badge;
use App\Normalizer\Normalizer;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class BadgeNormalizer extends Normalizer
{
    public function __construct(private UploaderHelper $uploaderHelper)
    {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!($object instanceof Badge)) {
            throw new \RuntimeException();
        }

        return [
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'image' => $this->uploaderHelper->asset($object),
            'theme' => $object->getTheme(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Badge && 'json' === $format;
    }
}
