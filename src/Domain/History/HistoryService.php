<?php

namespace App\Domain\History;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Repository\FormationRepository;
use App\Domain\History\DTO\FormationProgressDTO;
use App\Domain\History\Entity\Progress;
use App\Domain\History\Repository\ProgressRepository;

class HistoryService
{
    public function __construct(
        private readonly ProgressRepository  $progressRepository,
        private readonly FormationRepository $formationRepository,
    ) {
    }

    /**
     * @return Progress[]
     */
    public function getLastWatchedContent(User $user): array
    {
        return $this->progressRepository->findLastForUser($user);
    }

    public function getNextContentIdToWatch(User $user, Formation $formation): ?int
    {
        $ids = $formation->getModulesIds();
        $finishedIds = $this->progressRepository->findFinishedIdWithin($user, $ids);
        foreach ($ids as $id) {
            if (!in_array($id, $finishedIds)) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Liste le progrès de l'utilisateur fait sur l'ensemble de formations
     * @return FormationProgressDTO[]
     */
    public function getFormationsProgressForUser(User $user): array
    {
        $progress = $this->progressRepository->findSeenFormations($user);
        $formations = $this->formationRepository->findBy([
            'id' => array_keys($progress)
        ]);
        return array_map(fn(Formation $formation) => new FormationProgressDTO(
            $formation,
            $progress[$formation->getId()]
        ), $formations);
    }
}
