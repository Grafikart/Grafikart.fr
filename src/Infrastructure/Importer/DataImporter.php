<?php

namespace App\Infrastructure\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;

final class DataImporter
{

    private CoursesImporter $coursesImporter;
    private FormationImporter $formationImporter;
    private UserImporter $userImporter;

    public function __construct(
        CoursesImporter $coursesImporter,
        FormationImporter $formationImporter,
        UserImporter $userImporter
    )
    {

        $this->coursesImporter = $coursesImporter;
        $this->formationImporter = $formationImporter;
        $this->userImporter = $userImporter;
    }

    public function import(SymfonyStyle $io): void
    {
        $this->coursesImporter->import($io);
        $this->formationImporter->import($io);
        $this->userImporter->import($io);
    }

}
