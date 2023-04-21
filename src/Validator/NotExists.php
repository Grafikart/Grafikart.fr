<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NotExists extends Constraint
{
    public string $message = 'A record was found for {{ value }}';
    public string $field = 'id';

    /**
     * @var class-string<object>
     */
    public string $class = \stdClass::class;

    #[HasNamedArguments]
    public function __construct(
        string $class = \stdClass::class,
        array $groups = null,
        string $field = 'id',
        string $message = 'A record was found for {{ value }}'
    ) {
        parent::__construct([
            'field' => $field,
            'class' => $class,
            'message' => $message,
        ], $groups, null);
    }

    public function getRequiredOptions(): array
    {
        return ['class', 'field'];
    }
}
