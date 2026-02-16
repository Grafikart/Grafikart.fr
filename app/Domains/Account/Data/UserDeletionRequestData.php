<?php

namespace App\Domains\Account\Data;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class UserDeletionRequestData extends Data
{
    public function __construct(
        #[Required]
        #[Min(6)]
        public string $password,
        public ?string $reason = null,
    ) {}

}
