<?php

namespace App\Tests\Core\UploaderBundle;

use App\Core\UploaderBundle\PropertyGroupedDirectoryNamer;
use PHPUnit\Framework\TestCase;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class PropertyGroupedDirectoryNamerTest extends TestCase
{
    public function getTests()
    {
        yield [1000, 10, '100'];
        yield [1, 1000, '1'];
        yield [100, 1000, '1'];
    }

    /**
     * @dataProvider getTests
     */
    public function testModulo(int $id, int $modulo, string $expected): void
    {
        $object = (object) ['id' => $id];
        $namer = new PropertyGroupedDirectoryNamer();
        $namer->configure([
            'modulo' => $modulo,
            'property' => 'id',
        ]);
        $mapping = $this->getMockBuilder(PropertyMapping::class)->disableOriginalConstructor()->getMock();
        $this->assertEquals($expected, $namer->directoryName($object, $mapping));
    }
}
