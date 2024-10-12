<?php

namespace App\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Hydrate les relations lors de la denormalization, si on reçoit un entier pour remplir une entité.
 */
class EntityDenormalizer implements DenormalizerAwareInterface, DenormalizerInterface
{
    use DenormalizerAwareTrait;

    public const HYDRATE_RELATIONS = 'HYDRATE_RELATIONS';

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @param class-string<object> $type
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!is_numeric($data)) {
            return null;
        }

        return $this->em->getRepository($type)->find($data);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return ($context[self::HYDRATE_RELATIONS] ?? false) && (is_int($data) || is_string($data)) && strpos($type, '\\Entity');
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return null;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => true,
        ];
    }
}
