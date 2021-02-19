<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotExists extends Constraint
{
    public string $message = 'A record was found for {{ value }}';
    public string $field = 'id';

    /**
     * @var class-string<mixed>
     */
    public string $class = 'stdClass';

    public function getRequiredOptions(): array
    {
        return ['class', 'field'];
    }
}
