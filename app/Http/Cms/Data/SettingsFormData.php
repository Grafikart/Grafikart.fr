<?php

namespace App\Http\Cms\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapInputName(SnakeCaseMapper::class)]
class SettingsFormData extends Data
{
    public CarbonImmutable $liveAt;

    public function __construct(
        ?CarbonImmutable $liveAt = null,
    ) {
        $this->liveAt = $liveAt ?? now()->subDay();
    }
}
