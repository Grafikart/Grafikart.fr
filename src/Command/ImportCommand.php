<?php

namespace App\Command;

use App\Tool\Importer\CoursesImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    private CoursesImporter $importer;

    public function __construct(string $name = null, CoursesImporter $importer)
    {
        parent::__construct($name);
        $this->importer = $importer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Importe les données de l\'ancien site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->importer->import($io);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
