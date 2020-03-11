<?php

namespace App\Tests\Domain\Course\Entity;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use PHPUnit\Framework\TestCase;

class CourseTest extends TestCase
{

    public function getData()
    {
        yield [
            [
                ['PHP', null, true]
            ],
            [
                ['PHP', '5.4', true]
            ]
        ];
        yield [
            [],
            [
                ['PHP', '5.4', true]
            ]
        ];
        yield [
            [
                ['PHP', '5.4', true],
                ['Laravel', null, true]
            ],
            [
                ['Laravel', '5.4', true],
                ['Golang', null, true]
            ],
            ['PHP']
        ];
        yield [
            [
                ['PHP', '5.4', true],
                ['Laravel', null, true]
            ],
            [],
            ['Laravel', 'PHP']
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testUpdateVersion(
        array $courseTechnologies,
        array $syncTechnologies,
        array $expectedReturn = []
    ): void {
        // On crée le cours
        $course = new Course();
        $dict = []; // Dictionnaire qui simule l'entity manager (persiste les technologies de même nom

        // On injecte les technologies
        foreach ($courseTechnologies as [$name, $version, $secondary]) {
            $technology = (new Technology())->setName($name);
            $dict[$name] = $technology;
            $course->addTechnologyUsage(
                (new TechnologyUsage())
                    ->setVersion($version)
                    ->setSecondary($secondary)
                    ->setTechnology($technology)
            );
        }
        $course->setAuthor(new User());

        // On crée nos données
        $technologies = array_map(function ($technology) use ($dict) {
            [$name, $version, $secondary] = $technology;
            $technology = $dict[$name] ?? (new Technology());
            return $technology
                ->setName($name)
                ->setSecondary($secondary)
                ->setVersion($version);
        }, $syncTechnologies);

        $removed = $course->syncTechnologies($technologies);

        // On fait les assertions
        $this->assertSameSize($syncTechnologies, $course->getTechnologyUsages());
        foreach($syncTechnologies as $k => [$name, $version, $secondary]) {
            $this->assertEquals($name, $course->getTechnologyUsages()[$k]->getTechnology()->getName());
            $this->assertEquals($version, $course->getTechnologyUsages()[$k]->getVersion());
            $this->assertEquals($secondary, $course->getTechnologyUsages()[$k]->isSecondary());
        }

        $removedNames = array_map(fn (TechnologyUsage $u) => $u->getTechnology()->getName(), $removed);
        $this->assertEqualsCanonicalizing($expectedReturn, $removedNames);
    }

}
