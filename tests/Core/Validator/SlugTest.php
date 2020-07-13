<?php

namespace App\Tests\Core\Validator;

use App\Core\Validator\Slug;
use App\Tests\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\RegexValidator;

class SlugTest extends ValidatorTestCase
{
    public function dataProvider(): iterable
    {
        yield ['title', true];
        yield ['title-demo', true];
        yield ['title-demo-title-demo', true];
        yield ['title-demo-', false];
        yield ['-azeaze-azez', false];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSlugConstraint(string $slug, bool $shouldPass): void
    {
        $existsValidator = new RegexValidator();
        $context = $this->getContext(!$shouldPass);
        $existsValidator->initialize($context);
        $existsValidator->validate($slug, new Slug());
    }
}
