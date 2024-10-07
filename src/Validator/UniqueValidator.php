<?php

namespace App\Validator;

use App\Http\Admin\Data\CrudDataInterface;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param object|null $obj
     * @param Unique      $constraint
     */
    public function validate(mixed $obj, Constraint $constraint): void
    {
        if (null === $obj) {
            return;
        }

        if (!$constraint instanceof Unique) {
            throw new \RuntimeException(sprintf('%s ne peut pas valider des contraintes %s', self::class, $constraint::class));
        }

        if (!method_exists($obj, 'getId')) {
            throw new \RuntimeException(sprintf('%s ne peut pas être utilisé sur l\'objet %s car il ne possède pas de méthode getId()', self::class, $obj::class));
        }

        $accessor = new PropertyAccessor();
        /** @var class-string<object> $entityClass */
        $entityClass = $constraint->entityClass;
        if ($obj instanceof CrudDataInterface) {
            /** @var class-string<object> $entityClass */
            $entityClass = $obj->getEntity()::class;
        }
        $value = $accessor->getValue($obj, $constraint->field);
        $repository = $this->em->getRepository($entityClass);
        if ($repository instanceof AbstractRepository) {
            $result = $repository->findOneByCaseInsensitive([
                $constraint->field => $value,
            ]);
        } else {
            $result = $repository->findOneBy([
                $constraint->field => $value,
            ]);
        }

        if (
            null !== $result
            && (!method_exists($result, 'getId') || $result->getId() !== $obj->getId())
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->atPath($constraint->field)
                ->addViolation();
        }
    }
}
