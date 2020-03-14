<?php

namespace App\Domain\Course\EventListener;

use App\Core\Helper\PathHelper;
use App\Domain\Course\Entity\Course;
use App\Infrastructure\Storage\VideoMetaReader;

class CourseDurationUpdater
{

    private string $videosPath;
    private VideoMetaReader $metaReader;

    public function __construct(string $videosPath, VideoMetaReader $metaReader)
    {
        $this->videosPath = $videosPath;
        $this->metaReader = $metaReader;
    }

    public function updateDuration(Course $course): void
    {
        if (!empty($course->getVideoPath())) {
            $video = PathHelper::join($this->videosPath, $course->getVideoPath());
            if ($duration = $this->metaReader->getDuration($video)) {
                $course->setDuration($duration);
            }
        }
    }

}
