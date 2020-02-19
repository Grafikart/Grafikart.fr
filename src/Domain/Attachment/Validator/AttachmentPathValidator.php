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
        // My bad : DateTime::createFromFormat return false pour des années > 9999
        return $datetime && $datetime->format($format) === $value;
    }

}
