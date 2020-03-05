<?php

namespace App\Infrastructure\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;

interface TypeImporterInterface
{
    public function import(SymfonyStyle $io): void;

    public function support(string $type): bool;
}
