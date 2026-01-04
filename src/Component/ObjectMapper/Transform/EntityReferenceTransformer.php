<?php

namespace App\Component\ObjectMapper\Transform;

use App\Component\ObjectMapper\TransformCallableInterface;
use App\Component\ObjectMapper\TransformCallableWithContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final class EntityReferenceTransformer implements TransformCallableWithContextInterface
{

    public function __construct(private readonly EntityManagerInterface $em){
    }


    public function __invoke(mixed $value, object $source, ?object $target, array $context): mixed
    {
        if (is_null($value)) {
            return null;
        }
        if (is_int($value) && is_string($context['entity'])) {
            return $this->em->getReference($context['entity'], $value);
        }
        throw new \RuntimeException(sprintf('Impossible de convertir %s en %s', $value, $context['entity'] ?? 'unknown'));
    }
}
