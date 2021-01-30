<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

/**
 * @Annotation
 */
class Slug extends Regex
{
    public function __construct($options = [])
    {
        $options['pattern'] = '/^([a-z0-9A-Z]+\-)*[a-z0-9A-Z]+$/';
        parent::__construct($options);
    }

    public function validatedBy()
    {
        return RegexValidator::class;
    }
}
