<?php

namespace App\Domain\Attachment\Validator;

use Symfony\Component\Validator\Constraint;

class AttachmentExist extends Constraint
{
    public string $message = "Aucun attachment ne correspond à l'id {{ id }}";
}
