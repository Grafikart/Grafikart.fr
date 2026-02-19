<?php

namespace App\Http\Front\Data;

use Spatie\LaravelData\Data;

class RevisionData extends Data
{
    public function __construct(
        public string $content,
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'content' => ['required', 'min:10'],
        ];
    }
}
