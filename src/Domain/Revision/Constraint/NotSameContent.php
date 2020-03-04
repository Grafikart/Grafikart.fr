<?php

namespace App\Domain\Revision\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte permettant de s'assurer qu'une révision contient au moins une modification
 *
 * @Annotation
 */
class NotSameContent extends Constraint
{

    public string $message = "La révision doit posséder au moins une modification par rapport à l'article original";

    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }

}
