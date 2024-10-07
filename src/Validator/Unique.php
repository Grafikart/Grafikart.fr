<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Contrainte pour vérifier l'unicité d'un enregistrement.
 *
 * Pour fonctionner on part du principe que l'objet et l'entité aura une méthode "getId()"
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS)]
class Unique extends Constraint
{
    public string $message = 'Cette valeur est déjà utilisée';

    /**
     * @var class-string<object>|null
     */
    public ?string $entityClass = null;

    public string $field = '';

    #[HasNamedArguments]
    public function __construct(
        string $field = '',
        string $message = 'Cette valeur est déjà utilisée',
        ?string $entityClass = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([
            'field' => $field,
            'message' => $message,
            'entityClass' => $entityClass,
        ], $groups, $payload);
    }

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
