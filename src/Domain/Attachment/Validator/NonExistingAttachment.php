<?php

namespace App\Domain\Attachment\Validator;

use App\Domain\Attachment\Attachment;

/**
 * Représente un attachment qui n'existe pas en base de données.
 */
class NonExistingAttachment extends Attachment
{
    public function __construct(int $expectedId)
    {
        $this->id = $expectedId;
    }
}
