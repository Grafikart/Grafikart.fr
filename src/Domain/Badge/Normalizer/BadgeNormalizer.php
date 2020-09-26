<?php

namespace App\Domain\Badge\Normalizer;

use App\Domain\Badge\Entity\Badge;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BadgeNormalizer implements NormalizerInterface
{

    /**
     * @return array
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!($object instanceof Badge)) {
            throw new \RuntimeException();
        }

        return [
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'image' => 'https://www.grafikart.fr/uploads/badges/11.png',
            'theme' => $object->getTheme()
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Badge && $format === 'json';
    }
}
