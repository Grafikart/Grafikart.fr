<?php

namespace App\Domains\School\Data;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

final class SchoolImportData extends Data
{
    public function __construct(
        public readonly UploadedFile $csv,
        public readonly string $subject,
        public readonly string $message,
        public readonly bool $confirmed = false,
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'csv' => [
                'required',
                'file',
                'extensions:csv',
                'mimetypes:text/csv,application/csv,text/x-comma-separated-values,text/x-csv,text/plain',
                'max:2048',
            ],
            'subject' => ['required', 'string'],
            'message' => ['required', 'string'],
        ];
    }
}
