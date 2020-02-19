<?php

namespace App\Domain\Attachment\Validator;

/**
 * Permet de valider un chemin de dossier pour les attachment
 */
class AttachmentPathValidator
{

    static public function validate(string $value): bool
    {
        $format = 'Y/m';
        $datetime = DateTime::createFromFormat($format, $value);
        return $datetime->format($format) === $value;
    }

}
