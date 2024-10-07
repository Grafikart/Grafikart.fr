<?php

namespace App\Http\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CaptchaGuessDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d{1,3}-\d{1,3}$/')]
        public readonly string $response,
    ) {
    }
}
