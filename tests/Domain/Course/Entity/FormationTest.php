<?php

namespace App\Tests\Domain\Course\Entity;

use App\Domain\Course\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{

    public function testGetCoursesById() {
        // On essaie à vide
        $formation = new Formation();
        $courses = $formation->getCoursesById();
        $this->assertEquals([], $courses);

        // On remplie avec des cours
        $expected = Helper::makeCourse(12);
        $formation->addCourse(Helper::makeCourse(1));
        $formation->addCourse(Helper::makeCourse(2));
        $formation->addCourse($expected);
        $this->assertEquals($expected, $formation->getCoursesById()[12]);
    }

}
