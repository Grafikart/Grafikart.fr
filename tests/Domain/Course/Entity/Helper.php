<?php

namespace App\Tests\Domain\Course\Entity;

use App\Domain\Course\Entity\Course;

class Helper
{

    public static function makeCourse($id): Course
    {
        $course = new Course();
        $course->setId($id);
        $course->setTitle('Course' . $id);
        return $course;
    }

}
