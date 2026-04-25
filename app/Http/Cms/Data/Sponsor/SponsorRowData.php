<?php

namespace App\Http\Cms\Data\Sponsor;

use App\Domains\Sponsorship\SponsorType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SponsorRowData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public SponsorType $type,
        public string $url,
    ) {}
}
