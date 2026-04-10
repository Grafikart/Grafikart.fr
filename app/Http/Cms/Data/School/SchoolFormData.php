<?php

namespace App\Http\Cms\Data\School;

use App\Http\Cms\Data\OptionItemData;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class SchoolFormData extends Data
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $name = '',
        public readonly string $couponPrefix = '',
        public readonly int $credits = 0,
        public readonly string $emailSubject = '',
        public readonly string $emailMessage = '',
        public readonly ?OptionItemData $owner = null,
        /** @var array<SchoolStudentData> */
        public readonly array $students = [],
    ) {}

}
