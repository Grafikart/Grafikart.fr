<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Exists extends Constraint
{
    public string $message = 'No record found for {{ value }}';

    /**
     * @var class-string<mixed>
     */
    public string $class = 'stdClass';

    public function getRequiredOptions(): array
    {
        return ['class'];
    }
}
