<?php

namespace App\Http\Front\Data;

use App\Infrastructure\Spam\CaptchaRulesFactory;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class ContactData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $content,
    ) {}

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'content' => ['required', 'min:50'],
            ...CaptchaRulesFactory::rules(),
        ];
    }
}
