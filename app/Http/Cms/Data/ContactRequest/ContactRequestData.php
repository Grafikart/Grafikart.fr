<?php

namespace App\Http\Cms\Data\ContactRequest;

use App\Domains\Support\ContactRequest;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class ContactRequestData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $message,
        public readonly ?string $ip,
        public readonly \DateTimeInterface $createdAt,
    ) {}

}
