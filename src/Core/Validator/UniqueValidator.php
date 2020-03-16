<?php

namespace App\Core\Validator;

use App\Http\Admin\Data\CrudDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValidator extends ConstraintValidator
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string|null $value
     * @param Unique|Constraint $constraint
     */
    public function validate($obj, Constraint $constraint): void
    {
        if (null === $obj || '' === $obj) {
            return;
        }

        $accessor = new PropertyAccessor();
        $entityClass = $constraint->entityClass;
        if ($obj instanceof CrudDataInterface) {
            $entityClass = get_class($obj->getEntity());
        }
        $value = $accessor->getValue($obj, $constraint->field);
        $result = $this->em->getRepository($constraint->entityClass)->findOneBy([
            $constraint->field => $value
        ]);

        if ($result !== null && $result->getId() !== $obj->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->atPath($constraint->field)
                ->addViolation();
        }
    }
}
