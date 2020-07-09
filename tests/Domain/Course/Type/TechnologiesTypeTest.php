<?php

namespace App\Domain\Course\Type;

use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TechnologiesTypeTest extends TestCase
{
    private TechnologiesType $type;

    public function setUp(): void
    {
        /** @var TechnologyRepository|MockObject $repo */
        $repo = $this->getMockBuilder(TechnologyRepository::class)->disableOriginalConstructor()->getMock();
        $repo->expects($this->any())->method('findByNames')->willReturn([]);
        $this->type = new TechnologiesType($repo);
    }

    public function getData(): iterable
    {
        $technology = (new Technology())
            ->setName('PHP')
            ->setVersion('4.5');
        $technology2 = (new Technology())
            ->setName('WordPress')
            ->setVersion('5.3');
        $technology3 = (new Technology())
            ->setName('Laravel');
        yield [[], null];
        yield [[$technology, $technology2], 'PHP:4.5,WordPress:5.3'];
        yield [[$technology, $technology3], 'PHP:4.5,Laravel'];
        yield [[$technology, $technology3], 'PHP:4.5,Laravel'];
    }

    /**
     * @dataProvider getData
     */
    public function testTransform(?array $technologies, ?string $expected): void
    {
        $this->assertEquals($expected, $this->type->transform($technologies));
    }

    public function getDataForReverse(): iterable
    {
        $technology = (new Technology())
            ->setName('PHP')
            ->setVersion('4.5');
        $technology2 = (new Technology())
            ->setName('Laravel');
        yield [[], null];
        yield [[$technology, $technology2], 'PHP:4.5     ,   Laravel'];
        yield [[$technology, $technology2], 'PHP:4.5     ,   Laravel,    , , ,'];
        yield [[$technology, $technology2], 'PHP:4.5,Laravel'];
    }

    /**
     * @dataProvider getData
     * @dataProvider getDataForReverse
     */
    public function testReverseTransform(?array $expectedTechnologies, ?string $value): void
    {
        $technologies = $this->type->reverseTransform($value);
        $this->assertSameSize($expectedTechnologies, $technologies);
        foreach ($technologies as $k => $technology) {
            $this->assertEquals($expectedTechnologies[$k]->getName(), $technology->getName());
            $this->assertEquals($expectedTechnologies[$k]->getVersion(), $technology->getVersion());
        }
    }
}
