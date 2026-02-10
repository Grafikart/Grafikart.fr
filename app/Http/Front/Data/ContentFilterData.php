<?php

namespace App\Http\Front\Data;

use App\Domains\Course\DifficultyLevel;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Data;

class ContentFilterData extends Data
{
    public function __construct(
        public readonly ?string $technology = null,
        public readonly ?DifficultyLevel $level = null,
        public readonly ?int $duration = null,
        public readonly ?bool $premium = null,
        public readonly int $page = 1,
    ) {}

    public static function rules(): array
    {
        return [
            'type' => ['nullable', 'string', 'in:course,formation'],
            'technology' => ['nullable', 'string', 'exists:technologies,slug'],
            'level' => ['nullable', new Enum(DifficultyLevel::class)],
            'duration' => ['nullable', 'integer', 'in:10,20,60'],
            'premium' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function isActive(): bool
    {
        return $this->technology || $this->level || $this->duration || $this->premium;
    }

    public function perPage(): int
    {
        return $this->isActive() ? 28 : 26;
    }
}
