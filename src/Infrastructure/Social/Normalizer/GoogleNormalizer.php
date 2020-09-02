<?php

namespace App\Infrastructure\Social\Normalizer;

use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GoogleNormalizer implements NormalizerInterface
{
    /**
     * @param GoogleUser $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'github_id' => $object->getId(),
            'type' => 'Google',
            'username' => $object->getName(),
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof GoogleUser;
    }
}
