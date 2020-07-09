<?php

namespace App\Tests\Domain\Application\Entity;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    public function testMainTechnologies(): void
    {
        $content = new Course();
        $technology1 = new Technology();
        $technology2 = new Technology();
        $content->addTechnologyUsage(
            (new TechnologyUsage())
                ->setVersion('4.0.0')
                ->setTechnology($technology1)
        );
        $content->addTechnologyUsage(
            (new TechnologyUsage())
                ->setVersion('5.0.0')
                ->setTechnology($technology2)
        );
        $technologies = $content->getMainTechnologies();
        $this->assertInstanceOf(Technology::class, $technologies[0]);
        $this->assertEquals('4.0.0', $technologies[0]->getVersion());
        $this->assertEquals('5.0.0', $technologies[1]->getVersion());
    }

    public function testMainTechnologiesIgnoreSecondary(): void
    {
        $content = new Course();
        $technology1 = new Technology();
        $technology2 = new Technology();
        $content->addTechnologyUsage(
            (new TechnologyUsage())
                ->setVersion('4.0.0')
                ->setSecondary(true)
                ->setTechnology($technology1)
        );
        $content->addTechnologyUsage(
            (new TechnologyUsage())
                ->setVersion('5.0.0')
                ->setTechnology($technology2)
        );
        $technologies = $content->getMainTechnologies();
        $this->assertCount(1, $technologies);
    }
}
