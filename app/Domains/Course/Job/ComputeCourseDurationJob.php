<?php

namespace App\Domains\Course\Job;

use App\Domains\Course\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

final class ComputeCourseDurationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $courseId)
    {
    }

    public function handle(): void
    {
        $videoPath = Course::select('video_path')->find($this->courseId)->video_path;
        if (!$videoPath) {
            return;
        }

        $fullPath = Storage::disk('downloads')->path('videos/' . $videoPath);

        if (!file_exists($fullPath)) {
            return;
        }

        $result = Process::run([
            'ffprobe',
            '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'default=noprint_wrappers=1:nokey=1',
            $fullPath
        ]);

        if (!$result->successful()) {
            throw new \Exception('Cannot probe video duration : ' . $result->output());
        }
        $duration = (int)round((float)trim($result->output()));
        if ($duration > 0) {
            Course::where('id', $this->courseId)->update(['duration' => $duration]);
        }
    }
}
