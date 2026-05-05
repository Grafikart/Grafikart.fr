<?php

namespace App\Http\Cms\Data\ContactRequest;

use App\Domains\Support\ContactRequest;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class ContactRequestRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $message,
        public readonly ?string $ip,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    public static function fromModel(ContactRequest $contactRequest): self
    {
        return new self(
            id: $contactRequest->id,
            name: $contactRequest->name,
            email: $contactRequest->email,
            message: Str::limit($contactRequest->message, 150),
            ip: $contactRequest->ip,
            createdAt: $contactRequest->created_at,
        );
    }
}
