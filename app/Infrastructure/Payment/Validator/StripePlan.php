<?php

namespace App\Infrastructure\Payment\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Valide que le plan existe dans l'API de stripe.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class StripePlan extends Constraint
{
    public string $message = 'La formule "{{ value }}" n\'existe pas sur stripe.';
}
