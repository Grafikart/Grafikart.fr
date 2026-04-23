<?php

namespace App\Http\Front\Data\User;

use App\Models\User;
use App\Rules\CountryCode;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class ProfileUpdateData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $country = null,
        public bool $html5_player = false,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $userId = request()->user()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                $userId
                    ? Rule::unique(User::class)->ignore($userId)
                    : Rule::unique(User::class),
            ],
            'country' => ['nullable', 'string', new CountryCode],
        ];
    }
}
