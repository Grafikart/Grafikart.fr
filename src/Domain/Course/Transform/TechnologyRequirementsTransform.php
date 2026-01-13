<?php

namespace App\Domain\Course\Transform;

use App\Component\ObjectMapper\TransformCallableInterface;
use App\Domain\Course\Entity\Technology;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final readonly class TechnologyRequirementsTransform implements TransformCallableInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param int[]|null $value
     */
    public function __invoke(mixed $value, object $source, ?object $target): Collection
    {
        assert($target instanceof Technology);

        if (!$value) {
            foreach ($target->getRequirements() as $requirement) {
                $target->removeRequirement($requirement);
            }
            return new ArrayCollection();
        }

        $currentIds = $target->getRequirements()->map(fn (Technology $t) => $t->getId())->toArray();

        // Remove requirements that are no longer in the list
        foreach ($target->getRequirements() as $requirement) {
            if (!in_array($requirement->getId(), $value)) {
                $target->removeRequirement($requirement);
            }
        }

        // Add new requirements
        foreach ($value as $id) {
            if (!in_array($id, $currentIds)) {
                $target->addRequirement($this->em->getReference(Technology::class, $id));
            }
        }

        return $target->getRequirements();
    }
}
