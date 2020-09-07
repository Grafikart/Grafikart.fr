<?php

namespace App\Tests\Domain\Course\Entity;

use App\Domain\Course\Entity\Chapter;
use App\Domain\Course\Entity\Formation;
use PHPUnit\Framework\TestCase;

class ChapterTest extends TestCase
{
    public function dataProvider(): iterable
    {
        $formation = new Formation();
        $formation->setRawChapters([
            [
                'title' => 'Hello',
                'modules' => [1, 2, 4],
            ],
            [
                'title' => 'World',
                'modules' => [3],
            ],
        ]);
        for ($i = 1; $i < 5; ++$i) {
            $formation->addCourse(Helper::makeCourse($i));
        }
        yield [$formation, 2, 'Course2', 'Course3'];
        $formation = clone $formation;
        $formation->setRawChapters([
            [
                'title' => 'Hello',
                'modules' => [1, 18],
            ],
            [
                'title' => 'Hello',
                'modules' => [12, 18],
            ],
        ]);
        for ($i = 1; $i <= 18; ++$i) {
            $formation->addCourse(Helper::makeCourse($i));
        }
        yield [$formation, 2, 'Course18', 'Course12'];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testMakeCollection(Formation $formation, int $expectedChapters, string $firstChapterLastTitle, string $lastChapterFirstTitle): void
    {
        $chapters = Chapter::makeFromContent($formation);
        $this->assertCount($expectedChapters, $chapters);
        $this->assertInstanceOf(Chapter::class, $chapters[0]);
        $this->assertInstanceOf(Chapter::class, $chapters[1]);
        $this->assertEquals($chapters[0]->getModules()[1]->getTitle(), $firstChapterLastTitle);
        $this->assertEquals($chapters[1]->getModules()[0]->getTitle(), $lastChapterFirstTitle);
    }
}
