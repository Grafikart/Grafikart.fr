<?php

namespace App\Tests\Domain\Course\Repository;

use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Tests\FixturesTrait;
use App\Tests\KernelTestCase;

class TechnologyRepositoryTest extends KernelTestCase
{

    use FixturesTrait;

    private array $data = [];

    public function pickRandomTechnogies(): iterable
    {
        $size = 9;
        for($i = 0; $i < 5; $i++) {
            yield [collect(range(1, $size))->shuffle()->slice(0, rand(1, $size))->map(fn ($k) => "technology$k")->toArray()];
        }
    }

    /**
     * @dataProvider pickRandomTechnogies
     */
    public function testFindByNames(array $names): void
    {
        $this->data = $this->loadFixtures(['technologies']);
        $expectedEntities = array_map(fn (string $name) => $this->data[$name], $names);
        $names = array_map(fn (Technology $entity) => $entity->getName(), $expectedEntities);
        $entities = self::$container->get(TechnologyRepository::class)->findByNames($names);
        $this->assertSameSize($expectedEntities, $entities);
        foreach($expectedEntities as $entity) {
            $this->assertContains($entity, $entities);
        }
    }

}
