<?php

namespace App\Command;

use App\Infrastructure\Importer\DataImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    private DataImporter $importer;

    public function __construct(
        string $name = null,
        DataImporter $importer
    )
    {
        parent::__construct($name);
        $this->importer = $importer;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'Type de contenu à importer')
            ->setDescription('Importe les données de l\'ancien site');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        if ($type === 'reset') {
            $this->importer->resetContent($io);
        } elseif ($type === 'blog') {
            $this->importer->importBlog($io);
        } elseif ($type === 'tutoriels') {
            $this->importer->importCourse($io);
            // $this->importer->importFormation($io);
        } elseif ($type === 'users') {
            $this->importer->importUser($io);
        } elseif ($type === 'comments') {
            $this->importer->importComment($io);
        } else {
            $io->error('Type inconnu');
            return 1;
        }
        return 0;
    }
}
