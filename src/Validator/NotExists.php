<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class NotExists extends Constraint
{
    public string $message = 'A record was found for {{ value }}';
    public string $field = 'id';

    /**
     * @var class-string<object>
     */
    public string $class = \stdClass::class;

    public function getRequiredOptions(): array
    {
        return ['class', 'field'];
    }
}
