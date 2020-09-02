<?php

namespace App\Infrastructure\Social\Normalizer;

use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GithubNormalizer implements NormalizerInterface
{
    /**
     * @param GithubResourceOwner $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'github_id' => $object->getId(),
            'type' => 'Github',
            'username' => $object->getNickname(),
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof GithubResourceOwner;
    }
}
