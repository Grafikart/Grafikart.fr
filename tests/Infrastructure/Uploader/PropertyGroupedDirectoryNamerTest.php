<?php

namespace App\Tests\Infrastructure\Uploader;

use App\Infrastructure\Uploader\PropertyGroupedDirectoryNamer;
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
        $mapping = new PropertyMapping('file', 'file');
        $this->assertEquals($expected, $namer->directoryName($object, $mapping));
    }
}
