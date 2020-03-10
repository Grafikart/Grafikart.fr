<?php

namespace App\Tests\Domain\Course\EventListener;

use App\Domain\Course\Entity\Course;
use App\Tests\DatabaseTestCase;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;

/**
 * @IgnoreAnnotation("dataProvider")
 */
class CourseDurationUpdaterTest extends DatabaseTestCase
{

    public function getData (): iterable
    {
        yield ['video-10.mp4', 10];
        yield ['video-100.mp4', 100];
    }

    /**
     * @dataProvider getData
     */
    public function testUpdateDuration(string $video, int $expectedDuration): void
    {
        /** @var Course $course */
        ['course1' => $course] = $this->loadFixtures(['courses']);
        $course->setVideoPath($video);
        $this->em->flush();
        $this->assertEquals($expectedDuration, $course->getDuration());
    }



}
