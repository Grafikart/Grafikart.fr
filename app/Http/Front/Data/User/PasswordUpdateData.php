<?php

namespace App\Http\Front\Data\User;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class PasswordUpdateData extends Data
{
    public function __construct(
        #[Confirmed]
        #[Required]
        #[Min(6)]
        public string $password,
        public string $password_confirmation,
    ) {}

}
