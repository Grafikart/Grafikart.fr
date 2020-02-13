<?php

namespace App\Command;

use App\Infrastructure\Importer\DataImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    private DataImporter $importer;

    public function __construct(
        string $name = null,
        DataImporter $importer)
    {
        parent::__construct($name);
        $this->importer = $importer;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Importe les données de l\'ancien site');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->importer->import($io);
        $io->success('Import des contenus terminé');

        return 0;
    }
}
