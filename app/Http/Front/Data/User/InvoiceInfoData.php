<?php

namespace App\Http\Front\Data\User;

use Spatie\LaravelData\Data;

class InvoiceInfoData extends Data
{
    public function __construct(
        public string $info = '',
    ) {}
}
