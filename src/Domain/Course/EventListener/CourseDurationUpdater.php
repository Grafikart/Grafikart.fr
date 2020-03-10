<?php

namespace App\Domain\Course\EventListener;

use App\Domain\Course\Entity\Course;
use App\Helper\PathHelper;
use App\Infrastructure\Video\VideoMetaReader;

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
        $video = PathHelper::join($this->videosPath, $course->getVideoPath());
        $duration = (int)$this->metaReader->getDuration($video);
        $course->setDuration($duration);
    }

}
