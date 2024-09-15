<?php

namespace App\Domain\History\DTO;

use App\Domain\Course\Entity\Formation;

class FormationProgressDTO
{

    public function __construct(public readonly Formation $formation, public readonly int $progress)
    {
    }
}
