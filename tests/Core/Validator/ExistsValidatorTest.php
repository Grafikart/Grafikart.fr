<?php

namespace App\Tests\Core\Validator;

use App\Core\Validator\Exists;
use App\Core\Validator\ExistsValidator;
use App\Tests\ValidatorTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ExistsValidatorTest extends ValidatorTestCase
{
    public function dataProvider(): iterable
    {
        $constraint = new Exists(['class' => 'EntityClass']);
        // L'entityManager renvoie null (ne trouve aucun résultat)
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->any())->method('find')->willReturn(null);
        yield [false, null, $constraint, $em];
        yield [true, 100, $constraint, $em];

        // L'entityManager renvoie un résultat
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->any())->method('find')->with('EntityClass', 1)->willReturn(new \stdClass());
        yield [false, 1, $constraint, $em];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testNotSameValidator(bool $expectViolation, $value, Exists $constraint, EntityManagerInterface $em): void
    {
        // On mock l'entity manager
        // Le test
        $existsValidator = new ExistsValidator($em);
        $context = $this->getContext($expectViolation);
        $existsValidator->initialize($context);
        $existsValidator->validate($value, $constraint);
    }
}
