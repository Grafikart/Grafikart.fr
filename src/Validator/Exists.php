<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Exists extends Constraint
{
    public string $message = 'No record found for {{ value }}';

    /**
     * @var class-string<object>
     */
    public string $class = \stdClass::class;

    public function getRequiredOptions(): array
    {
        return ['class'];
    }
}
