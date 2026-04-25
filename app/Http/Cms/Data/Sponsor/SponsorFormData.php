<?php

namespace App\Http\Cms\Data\Sponsor;

use App\Concerns\Media\MapMedia;
use App\Domains\Sponsorship\SponsorType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SponsorFormData extends Data
{
    public CarbonImmutable $createdAt;

    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $url = '',
        public string $content = '',
        public SponsorType $type = SponsorType::Sponsor,
        #[MapMedia()]
        public ?string $logo = null,
        ?CarbonImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? now();
    }
}
