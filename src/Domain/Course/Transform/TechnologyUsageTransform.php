<?php

namespace App\Domain\Course\Transform;

use App\Component\ObjectMapper\TransformCallableInterface;
use App\Domain\Application\Entity\Content;
use App\Domain\Course\DTO\ContentTechnologyDTO;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Attache les nouveaux usages à l'entité et supprime les liens qui ne sont plus utilisés.
 */
#[Autoconfigure(public: true)]
final readonly class TechnologyUsageTransform implements TransformCallableInterface
{

    public function __construct(private EntityManagerInterface $em){
    }

    /**
     * @param ContentTechnologyDTO $value
     * @param object $source
     * @param Content|null $target
     * @return mixed
     */
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        assert($target instanceof Content);
        $technologyIds = array_map(fn (ContentTechnologyDTO $dto) => $dto->id, $value);

        // On supprime les usages qui ne sont plus dans la liste
        foreach ($target->getTechnologyUsages() as $usage) {
            if (!in_array($usage->getTechnology()->getId(), $technologyIds)) {
                $target->removeTechnologyUsage($usage);
            }
        }

        // On ajoute/met à jour les usages
        foreach ($value as $dto) {
            $usage = $this->findUsage($target, $dto->id);
            if ($usage === null) {
                $usage = new TechnologyUsage()
                    ->setTechnology($this->em->getReference(Technology::class, $dto->id));
                $target->addTechnologyUsage($usage);
            }
            $usage->setVersion($dto->version)
                ->setSecondary(!$dto->primary);
        }

        return $target->getTechnologyUsages();
    }

    private function findUsage(Content $entity, int $technologyId): ?TechnologyUsage
    {
        foreach ($entity->getTechnologyUsages() as $usage) {
            if ($usage->getTechnology()->getId() === $technologyId) {
                return $usage;
            }
        }

        return null;
    }
}
