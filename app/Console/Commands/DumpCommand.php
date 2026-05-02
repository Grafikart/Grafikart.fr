<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DumpCommand extends Command
{
    protected $signature = 'app:dump';

    protected $description = 'Export de la base de données vers le stockage snapshots';

    public function handle(): int
    {
        $this->info('Export de la base de données');

        $dumpFile = storage_path('dump.tar.gz');
        $connection = config('database.connections.pgsql');
        $process = new Process([
            'pg_dump',
            '-U', $connection['username'],
            '-Ft',
            '--compress=gzip:9',
            '-h', $connection['host'],
            '--exclude-table=old_*',
            '-f', $dumpFile,
            $connection['database'],
        ]);
        $process->setEnv(['PGPASSWORD' => $connection['password']]);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error("Impossible d'exporter la base de données");
            $this->error($process->getErrorOutput());
            $this->fail();
        }

        $this->info('Upload en cours...');
        $stream = fopen($dumpFile, 'r');
        if ($stream === false) {
            $this->fail("Impossible de lire le fichier \"$dumpFile\"");
        }

        $date = now()->format('Y-m-d');
        try {
            Storage::disk('snapshots')->writeStream("grafikart-{$date}.tar.gz", $stream);
        } finally {
            @unlink($dumpFile);
        }

        $this->info('La base de données a bien été sauvegardée');
        return 0;
    }
}
