<?php

namespace App\Domains\School\Data;

use App\Domains\School\InvalidCSVException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;
use League\Csv\SyntaxError;
use Spatie\LaravelData\Data;

class SchoolImportRow extends Data
{
    public function __construct(
        public string $email,
        public int $months,
    ) {}

    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'months' => ['required', 'integer', 'gt:0', 'lt:24'],
        ];
    }

    /**
     * @return SchoolImportRow[]
     */
    public static function fromCSV(UploadedFile $file): array
    {
        $csvReader = Reader::from($file);
        $csvReader->setDelimiter(',');
        $csvReader->setHeaderOffset(0);

        try {
            $headers = $csvReader->getHeader();
        } catch (SyntaxError) {
            throw new InvalidCSVException('Le fichier CSV est invalide, en-tête non présente.');
        }

        if ($headers !== ['email', 'months']) {
            throw new InvalidCSVException('Le fichier CSV doit contenir les en-têtes exactes: email,months');
        }

        /** @var SchoolImportRow[] $rows */
        $rows = [];
        foreach ($csvReader->getRecords() as $line => $record) {
            try {
                $rows[] = SchoolImportRow::validateAndCreate($record);
            } catch (ValidationException $exception) {
                throw new InvalidCSVException(sprintf('Erreur sur la ligne %d, %s', $line + 1, $exception->getMessage()));
            }
        }

        return $rows;
    }
}
