<?php


namespace App\Tests\Core\Validator;


use App\Core\Validator\Unique;
use App\Core\Validator\UniqueValidator;
use App\Tests\ValidatorTestCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class UniqueValidatorTest extends ValidatorTestCase
{

    public function dataProvider(): iterable
    {
        $obj = new FakeObjectWithSlug('myslug');
        $obj1 = new FakeObjectWithSlug('myslug', 1);
        $obj2 = new FakeObjectWithSlug('myslug', 2);
        yield [$obj, null, false];
        yield [$obj1, $obj1, false];
        yield [$obj1, $obj2, true];
    }

    /**
     * @param FakeObjectWithSlug $value
     * @dataProvider dataProvider
     */
    public function testUniqueValidator(
        FakeObjectWithSlug $value,
        ?FakeObjectWithSlug $repositoryResult,
        $expectedViolation
    ): void {
        // On crée le mock de repository qui renvera un count spécifique
        $repository = $this->getMockBuilder(ServiceEntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['slug' => $value->slug])
            ->willReturn($repositoryResult);
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->with('Demo')
            ->willReturn($repository);

        // On génère notre contrainte
        $existsValidator = new UniqueValidator($em);
        $context = $this->getContext($expectedViolation);
        $existsValidator->initialize($context);
        $existsValidator->validate($value, new Unique(['entityClass' => 'Demo', 'field' => 'slug']));
    }

}
