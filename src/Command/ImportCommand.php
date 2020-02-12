<?php

namespace App\Command;

use App\Infrastructure\Importer\CoursesImporter;
use App\Infrastructure\Importer\FormationImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    private CoursesImporter $courseImporter;
    private FormationImporter $formationImporter;

    public function __construct(
        string $name = null,
        CoursesImporter $courseImporter,
        FormationImporter $formationImporter)
    {
        parent::__construct($name);
        $this->courseImporter = $courseImporter;
        $this->formationImporter = $formationImporter;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Importe les données de l\'ancien site');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // $this->courseImporter->import($io);
        $this->formationImporter->import($io);

        $io->success('Import des contenus terminé');

        return 0;
    }
}
