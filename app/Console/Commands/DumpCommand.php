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

        // Generate the dump
        $dumpFile = storage_path('dump.tar');
        $connection = config('database.connections.pgsql');
        $process = new Process([
            'pg_dump',
            '-U', $connection['username'],
            '-Ft',
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

        // Gzip the dump
        $this->info('Compressing');
        $process = new Process(['gzip', $dumpFile, $dumpFile.'.gz']);
        $process->run();
        @unlink($dumpFile);
        $dumpFile .= '.gz';
        if (! $process->isSuccessful()) {
            $this->fail('Cannot compress pgsql dump '.$process->getErrorOutput());
        }

        // Upload
        $this->info('Upload en cours...');
        $stream = fopen($dumpFile, 'r');
        if ($stream === false) {
            $this->fail("Impossible de lire le fichier \"$dumpFile\"");
        }

        $date = now()->format('Y-m-d');
        try {
            Storage::disk('snapshots')->writeStream("grafikart-{$date}.dump", $stream);
        } catch (\Exception $e) {
            $this->fail("Impossible d'uploader le fichier ".$e->getMessage());
        } finally {
            @unlink($dumpFile);
        }

        $this->info('La base de données a bien été sauvegardée');

        return 0;
    }
}
