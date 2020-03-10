<?php

namespace App\Infrastructure\Video;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VideoMetaReader
{

    private string $ffprobeBinary;

    public function __construct(string $ffprobeBinary = 'ffprobe')
    {
        $this->ffprobeBinary = $ffprobeBinary;
    }

    public function getDuration(string $videoPath): ?int
    {
        if (!file_exists($videoPath)) {
            return null;
        }
        $process = new Process([
            'ffprobe',
            '-v',
            'error',
            '-show_entries',
            'format=duration',
            '-of',
            'default=noprint_wrappers=1:nokey=1',
            $videoPath
        ]);
        try {
            $process->mustRun();
            return (int)$process->getOutput();
        } catch (ProcessFailedException $exception) {
            $error = sprintf(
                "Impossible de récupérer la durée de la vidéo %s, %s",
                $videoPath,
                $exception->getMessage()
            );
            throw new \RuntimeException($error);
        }
    }

}
