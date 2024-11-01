<?php

namespace App\Http\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class SchoolImportConfirmRequestData
{
    public function __construct(
        #[NotBlank()]
        public readonly string $content = '',
    ) {
    }
}
