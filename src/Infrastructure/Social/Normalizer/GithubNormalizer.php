<?php

namespace App\Infrastructure\Social\Normalizer;

use App\Normalizer\Normalizer;
use League\OAuth2\Client\Provider\GithubResourceOwner;

class GithubNormalizer extends Normalizer
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

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof GithubResourceOwner;
    }
}
