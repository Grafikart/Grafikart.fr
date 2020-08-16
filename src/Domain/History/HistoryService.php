<?php

namespace App\Domain\History;

use App\Domain\Auth\User;
use App\Domain\History\Entity\Progress;
use App\Domain\History\Repository\ProgressRepository;

class HistoryService
{

    /**
     * @var ProgressRepository
     */
    private ProgressRepository $progressRepository;

    public function __construct(ProgressRepository  $progressRepository)
    {

        $this->progressRepository = $progressRepository;
    }

    /**
     * @param User $user
     * @return Progress[]
     */
    public function getLastWatchedContent(User $user): array
    {
        return $this->progressRepository->findLastForUser($user);
    }
}
