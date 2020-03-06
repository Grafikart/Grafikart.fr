<?php

namespace App\Command;

use App\Infrastructure\Importer\DataImporterHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    private DataImporterHandler $importerHandler;

    public function __construct(DataImporterHandler $importerHandler, string $name = null)
    {
        parent::__construct($name);
        $this->importerHandler = $importerHandler;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'Type de contenu à importer')
            ->setDescription('Importe les données de l\'ancien site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');

        if ($type === 'reset') {
            $this->importerHandler->resetContent();
        } else {
            if (false === $this->importerHandler->getImporter($type, $io)) {
                $io->error('Type inconnu');

                return 1;
            }
        }

        return 0;
    }
}
