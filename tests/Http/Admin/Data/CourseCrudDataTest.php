<?php

namespace App\Tests\Http\Admin\Data;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Http\Admin\Data\CourseCrudData;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CourseCrudDataTest extends TestCase
{
    private MockObject $em;

    /**
     * @var array<string,Technology>
     */
    private array $technologies = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->technologies = [];
    }

    public function getData()
    {
        yield [
            ['PHP'],
            ['PHP'],
            [],
        ];
        yield [
            ['PHP', 'Laravel'],
            ['Golang', 'Laravel'],
            ['PHP'],
        ];
        yield [
            ['PHP', 'Laravel'],
            [],
            ['PHP', 'Laravel'],
        ];
    }

    /**
     * On vérifie que la classe déclenche bien la demande de suppression au niveau de l'entityManager.
     *
     * @dataProvider getData
     */
    public function testRemoveUsageFromEntityManager(
        array $courseTechnologies,
        array $dataTechnologies,
        array $expectedRemoved = []
    ): void {
        // On crée le cours
        $course = new Course();

        // On injecte les technologies
        foreach ($courseTechnologies as $name) {
            $course->addTechnologyUsage(
                (new TechnologyUsage())->setTechnology($this->getTechnology($name))
            );
        }
        $course->setAuthor(new User());

        // On crée notre objet data
        $data = new CourseCrudData($course);
        $data->mainTechnologies = array_map([$this, 'getTechnology'], $dataTechnologies);
        $data->setEntityManager($this->em);

        // Les assertions
        $argumentAssertions = collect($expectedRemoved)
            ->map(fn (string $name) => $this->getTechnology($name))
            ->map(function (Technology $t) {
                return [$this->callback(fn (TechnologyUsage $u) => $u->getTechnology() === $t)];
            });
        $this->em
            ->expects($this->exactly(count($expectedRemoved)))
            ->method('remove')
            ->withConsecutive(...$argumentAssertions);

        // On lance l'hydration
        $data->hydrate();
    }

    private function getTechnology(string $name): Technology
    {
        $key = strtolower($name);
        if (!isset($this->technologies[$key])) {
            $this->technologies[$key] = (new Technology())->setName($name);
        }

        return $this->technologies[$key];
    }
}
