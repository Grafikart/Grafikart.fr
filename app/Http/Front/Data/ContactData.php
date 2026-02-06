<?php

namespace App\Http\Front\Data;

use Spatie\LaravelData\Data;

class ContactData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $content,
    ) {}
}
