<?php

namespace App\Tests\Http\Admin\Data;

use PHPUnit\Framework\TestCase;

class AutomaticCrudDataTest extends TestCase
{

    public function dataProvider(): iterable
    {
        $obj = new \stdClass();
        $obj->name = 'Hello';
        yield [$obj, 'Hello'];
        yield [new FakeEntity('Hello'), 'Hello'];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAutomaticHydratation($obj, string $expected): void
    {
        $data = new FakeCrudData($obj);
        $this->assertEquals($expected, $data->name);
    }

    public function testReverseHydratation(): void
    {
        $obj = new \stdClass();
        $obj->name = 'john';
        $data = new FakeCrudData($obj);
        $data->name = 'Hello';
        $data->hydrate();
        $this->assertEquals('Hello', $obj->name);
    }

    public function testReverseHydratationWithSetters(): void
    {
        $obj = new FakeEntity('john');
        $data = new FakeCrudData($obj);
        $data->name = 'Hello';
        $data->hydrate();
        $this->assertEquals('Hello', $obj->getName());
    }


}
