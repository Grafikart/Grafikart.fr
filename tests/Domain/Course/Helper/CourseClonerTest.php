<?php

namespace App\Tests\Domain\Course\Helper;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Domain\Course\Helper\CourseCloner;
use PHPUnit\Framework\TestCase;

class CourseClonerTest extends TestCase
{
    public function testCourseCloning(): void
    {
        // On crée le cours
        $course = (new Course())
            ->setTitle('Hello World')
            ->setSource('fake.zip');
        $course->addTechnologyUsage(
            (new TechnologyUsage())->setTechnology($this->getTechnology('t1'))
        );
        $course->addTechnologyUsage(
            (new TechnologyUsage())->setTechnology($this->getTechnology('t2'))
        );

        // On clone
        $clonedCourse = CourseCloner::clone($course);

        // On vérifie que les objets sont similaires mais pas identiques
        $this->assertEquals($course->getTitle(), $clonedCourse->getTitle());
        $this->assertNotSame($course, $clonedCourse);
        $this->assertNull($clonedCourse->getSource());
        $this->assertNull($clonedCourse->getYoutubeId());
        $this->assertNotSame($course->getTechnologyUsages()[0], $clonedCourse->getTechnologyUsages()[0]);
    }

    private function getTechnology(string $name): Technology
    {
        return (new Technology())->setName($name);
    }
}
