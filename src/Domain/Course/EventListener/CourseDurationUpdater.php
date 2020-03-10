<?php

namespace App\Domain\Course\EventListener;

use App\Domain\Course\Entity\Course;

class CourseDurationUpdater
{

    public function updateDuration(Course $course): void
    {
        // TODO : trouver la durée de la vidéo ;)
        $course->setDuration(0);
    }

}
