<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Exists extends Constraint
{
    public string $message = 'No record found for {{ value }}';

    #[HasNamedArguments]
    public function __construct(
        /**
         * @var class-string<object>
         */
        public string $class = \stdClass::class,
        ?array $groups = null,
        mixed $payload = null
    )
    {
        parent::__construct(null, $groups, $payload);
    }
}
