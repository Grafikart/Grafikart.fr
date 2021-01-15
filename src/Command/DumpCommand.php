<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class DumpCommand extends Command
{
    protected static $defaultName = 'app:dump';
    private EntityManagerInterface $em;
    private string $dumpPath;
    private FilesystemInterface $filesystem;

    public function __construct(EntityManagerInterface $em, string $projectPath, FilesystemInterface $filesystem)
    {
        parent::__construct();
        $this->em = $em;
        $this->dumpPath = $projectPath;
        $this->filesystem = $filesystem;
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
