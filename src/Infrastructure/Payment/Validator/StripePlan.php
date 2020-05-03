<?php

namespace App\Infrastructure\Payment\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class StripePlan extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'La formule "{{ value }}" n\'existe pas sur stripe.';
}
