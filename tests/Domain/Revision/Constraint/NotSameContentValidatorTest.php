<?php

namespace App\Tests\Domain\Revision\Constraint;

use App\Domain\Blog\Post;
use App\Domain\Revision\Constraint\NotSameContent;
use App\Domain\Revision\Constraint\NotSameContentValidator;
use App\Domain\Revision\Revision;
use App\Tests\ValidatorTestCase;

class NotSameContentValidatorTest extends ValidatorTestCase
{
    public function dataProvider(): iterable
    {
        $post = (new Post())->setContent('aa');
        $revision = (new Revision())->setTarget($post)->setContent('bb');
        yield [false, $revision];
        $revision2 = (new Revision())->setTarget($post)->setContent('aa');
        yield [true, $revision2];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testNotSameValidator(bool $expectViolation, Revision $revision): void
    {
        $context = $this->getContext($expectViolation);
        $validator = new NotSameContentValidator();
        $validator->initialize($context);
        $validator->validate($revision, new NotSameContent());
    }
}
