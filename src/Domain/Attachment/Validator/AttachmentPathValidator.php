<?php

namespace App\Domain\Attachment\Validator;

/**
 * Permet de valider un chemin de dossier pour les attachment
 */
class AttachmentPathValidator
{

    static public function validate(string $value): bool
    {
        return preg_match('/^2\d{3}\/(1[0-2]|0[1-9])$/', $value) > 0;
    }

}
