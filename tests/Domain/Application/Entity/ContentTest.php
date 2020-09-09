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

    public function testExcerpt(): void
    {
        $course = new Course();
        $course->setContent("Dans ce tutoriel je vous propose de réfléchir à la mise en place d'un système de permissions en PHP. L'objectif est de mettre en place un système qui nous permettra de vérifier si l'utilisateur est autorisée à effectuer une action spécifique au sein de notre application.

00:00 Présentation des systèmes de permissions existants
09:50 On crée notre propre système

## Les stratégies

A travers mon exploration de différents frameworks / technologies j'ai pu découvrir différentes approches du problèmes.

### Permissions hiérarchique");
        $this->assertEquals("Dans ce tutoriel je vous propose de réfléchir à la mise en place d'un système de permissions en PHP. L'objectif est de mettre en place un système qui nous permettra de vérifier si l'utilisateur est autorisée à effectuer une action spécifique au sein de notre application.", $course->getExcerpt());
    }
}
