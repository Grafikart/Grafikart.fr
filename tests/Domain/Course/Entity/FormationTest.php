<?php

namespace App\Tests\Domain\Course\Entity;

use App\Domain\Course\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetCoursesById()
    {
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

    public function testNextCourseId()
    {
        $formation = new Formation();
        for ($i = 1; $i <= 5; ++$i) {
            $formation->addCourse(Helper::makeCourse($i));
        }
        $formation->setRawChapters([
            ['title' => 'Demo', 'modules' => [4, 3]],
            ['title' => 'Demo', 'modules' => [1, 5, 2]],
        ]);
        $this->assertEquals(null, $formation->getNextCourseId(2));
        $this->assertEquals(3, $formation->getNextCourseId(4));
        $this->assertEquals(1, $formation->getNextCourseId(3));
        $this->assertEquals(2, $formation->getNextCourseId(5));
    }
}
