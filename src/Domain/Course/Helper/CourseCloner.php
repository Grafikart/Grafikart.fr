<?php

namespace App\Domain\Course\Helper;

use App\Domain\Course\Entity\Course;
use DateTime;

/**
 * Permet de dupliquer un cours en prenant en compte les associations
 */
class CourseCloner
{

    public static function clone(Course $course): Course
    {
        $clone = clone $course;
        $clone->setSource(null);
        $clone->setYoutubeId(null);
        $clone->setCreatedAt(new DateTime('@' . ($course->getCreatedAt()->getTimestamp() + 24 * 3600)));
        $usages = $clone->getTechnologyUsages();
        $clone->syncTechnologies([]);
        foreach($usages as $usage) {
            $clone->addTechnologyUsage(clone $usage);
        }
        return $clone;
    }
}
