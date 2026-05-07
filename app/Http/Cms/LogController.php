<?php

namespace App\Http\Cms;

use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\Process\Process;

final readonly class LogController
{
    public function index(): Response
    {
        $logPath = storage_path('logs/laravel.log');
        $output = '';

        if (file_exists($logPath)) {
            $process = Process::fromShellCommandline(
                'tail -n 1000 '.escapeshellarg($logPath).' | grep -A 20 -B 2 "#0 "'
            );
            $process->run();
            $output = $process->getOutput();
        }

        return Inertia::render('logs/index', ['output' => $output]);
    }
}
