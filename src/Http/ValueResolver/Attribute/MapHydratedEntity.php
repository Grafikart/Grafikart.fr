<?php

namespace App\Http\ValueResolver\Attribute;

use App\Http\ValueResolver\EntityHydratorValueResolver;
use Attribute;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints\GroupSequence;

/**
 * Attribut permettant une fusion entre MapEntity & MapRequestPayload
 * - Récupère l'entité depuis la base de donnée (comme MapEntity)
 * - Injecte les données provenant de la requête
 * - Valide les données
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class MapHydratedEntity extends ValueResolver
{

    public ArgumentMetadata $metadata;

    public function __construct(
        public readonly array|string|null $acceptFormat = null,
        public readonly array $groups = [],
        public readonly string|GroupSequence|array|null $validationGroups = null,
    ) {
        parent::__construct(EntityHydratorValueResolver::class);
    }

}
