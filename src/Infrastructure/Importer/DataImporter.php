<?php

namespace App\Infrastructure\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;

final class DataImporter
{

    private CoursesImporter $coursesImporter;
    private FormationImporter $formationImporter;
    private UserImporter $userImporter;
    private BlogImporter $blogImporter;

    public function __construct(
        CoursesImporter $coursesImporter,
        FormationImporter $formationImporter,
        UserImporter $userImporter,
        BlogImporter $blogImporter
    )
    {
        $this->coursesImporter = $coursesImporter;
        $this->formationImporter = $formationImporter;
        $this->userImporter = $userImporter;
        $this->blogImporter = $blogImporter;
    }

    public function import(SymfonyStyle $io): void
    {
        // $this->coursesImporter->import($io);
        // $this->formationImporter->import($io);
        $this->userImporter->import($io);
        $this->blogImporter->import($io);
    }

}
