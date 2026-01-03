<?php

namespace App\Domain\Course\Service;

use App\Domain\Application\Entity\Content;
use App\Domain\Course\DTO\ContentTechnologyDTO;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use Doctrine\ORM\EntityManagerInterface;

readonly class TechnologySyncService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * Attache les nouveaux usages à l'entité et supprime les liens qui ne sont plus utilisés.
     *
     * @param ContentTechnologyDTO[] $technologies
     */
    public function sync(Content $entity, array $technologies): void
    {
        $technologyIds = array_map(fn (ContentTechnologyDTO $dto) => $dto->id, $technologies);

        // On supprime les usages qui ne sont plus dans la liste
        foreach ($entity->getTechnologyUsages() as $usage) {
            if (!in_array($usage->getTechnology()->getId(), $technologyIds)) {
                $entity->removeTechnologyUsage($usage);
            }
        }

        // On ajoute/met à jour les usages
        foreach ($technologies as $dto) {
            $usage = $this->findUsage($entity, $dto->id);
            if ($usage === null) {
                $usage = new TechnologyUsage()
                    ->setTechnology($this->em->getReference(Technology::class, $dto->id));
                $entity->addTechnologyUsage($usage);
            }
            $usage->setVersion($dto->version)
                ->setSecondary(!$dto->primary);
        }
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
