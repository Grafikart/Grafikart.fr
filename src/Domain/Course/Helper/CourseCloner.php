<?php

namespace App\Domain\Course\Helper;

use App\Domain\Course\Entity\Course;

/**
 * Permet de dupliquer un cours en prenant en compte les associations.
 */
class CourseCloner
{
    public static function clone(Course $course): Course
    {
        $clone = new Course();
        $clone->setSlug($course->getSlug());
        $clone->setAuthor($course->getAuthor());
        $clone->setImage($course->getImage());
        $clone->setYoutubeThumbnail($course->getYoutubeThumbnail());
        $clone->setOnline($course->isOnline());
        $clone->setVideoPath($course->getVideoPath());
        $clone->setLevel($course->getLevel());
        $clone->setDemo($course->getDemo());
        $clone->setContent($course->getContent());
        $clone->setCreatedAt(
            (new \DateTimeImmutable(
                '@'.$course->getCreatedAt()->getTimestamp().' +1 day'
            ))->setTimezone($course->getCreatedAt()->getTimezone())
        );
        $usages = $course->getTechnologyUsages();

        foreach ($usages as $usage) {
            $clone->addTechnologyUsage(clone $usage);
        }

        return $clone;
    }
}
