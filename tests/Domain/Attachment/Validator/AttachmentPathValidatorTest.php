<?php

namespace App\Tests\Domain\Attachment\Validator;

use App\Domain\Attachment\Validator\AttachmentPathValidator;
use PHPUnit\Framework\TestCase;

class AttachmentPathValidatorTest extends TestCase
{

    public function obviouslyBadValues(): array
    {
        return [
            ['azeea', false],
            ['azeeaz/azezae', false]
        ];
    }

    public function goodValues(): array
    {
        return [
            ['2020/01', true],
            ['2018/12', true],
            ['2048/03', true]
        ];
    }

    public function sneakyBadValues(): array
    {
        return [
            ['2020/1', false],
            ['208/20', false],
            ['1900/12', false],
            ['2020/99', false],
            ['2020/20', false],
            ['2020/00', false],
            ['2020/13', false]
        ];
    }

    /**
     * @dataProvider obviouslyBadValues
     * @dataProvider goodValues
     * @dataProvider sneakyBadValues
     */
    public function testData(string $value, bool $expected): void
    {
        $this->assertEquals($expected, AttachmentPathValidator::validate($value));
    }



}
