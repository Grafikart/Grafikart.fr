<?php

namespace App\Http\Controller;

use App\Domain\Premium\Entity\Plan;
use App\Domain\Premium\Repository\PlanRepository;

class PlanController extends AbstractController
{
    /**
     * @return Plan[]
     */
    public function plans(PlanRepository $planRepository): array
    {
        return $planRepository->findall();
    }
}
