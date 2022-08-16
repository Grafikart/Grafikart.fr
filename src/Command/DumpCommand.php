<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand('app:dump')]
class DumpCommand extends Command
{
    protected static $defaultName = 'app:dump';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly string $dumpPath,
        private readonly FilesystemOperator $filesystem
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('Export de la base de données');
        $dumpFile = $this->dumpPath.'/dump.tar';

        // On génère le dump SQL
        $params = $this->em->getConnection()->getParams();
        $process = new Process(['pg_dump', '-U', $params['user'], '-Ft', '-h', $params['host'], '-f', $dumpFile]);
        $process->setEnv(['PGPASSWORD' => $params['password']]);
        $process->run();

        if (!$process->isSuccessful()) {
            $io->error("Impossible d'exporter la base de données");
            $io->error($process->getErrorOutput());

            return 1;
        }

        // On sauvegarde le fichier dans notre backup storage
        $io->writeln('Upload en cours...');
        $process = new Process(['gzip', $dumpFile]);
        $process->run();
        $stream = fopen($dumpFile.'.gz', 'r');
        if (false === $stream) {
            throw new \Exception("Impossible de lire le fichier \"$dumpFile\"");
        }
        $date = date('Y-m-d');
        try {
            $this->filesystem->writeStream("grafikart-{$date}.tar.gz", $stream);
        } catch (\Exception $e) {
            unlink($dumpFile.'.gz');
            throw $e;
        }
        unlink($dumpFile.'.gz');
        $io->success('La base de donnée a bien été sauvegardée');

        return 0;
    }
}
