<?php

namespace App\Infrastructure\Social\Normalizer;

use App\Normalizer\Normalizer;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\GoogleUser;

class GoogleNormalizer extends Normalizer
{
    /**
     * @param GoogleUser $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'google_id' => $object->getId(),
            'type' => 'Google',
            'username' => $object->getName(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof GoogleUser;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            GoogleUser::class => true
        ];
    }
}
