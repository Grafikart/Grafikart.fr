<?php

namespace App\Domain\Course\EventListener;

use App\Domain\Course\Entity\Course;
use App\Helper\PathHelper;
use App\Infrastructure\Storage\VideoMetaReader;

class CourseDurationUpdater
{
    public function __construct(private string $videosPath, private VideoMetaReader $metaReader)
    {
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
