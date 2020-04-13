<?php

namespace App\Http\Normalizer;

use App\Domain\Auth\User;
use App\Http\Encoder\PathEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserPathNormalizer implements NormalizerInterface
{

    /**
     * @inheritDoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof User) {
            return [
                'path' => 'user_show',
                'params' => ['id' => $object->getId()]
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof User && $format === PathEncoder::FORMAT;
    }
}
