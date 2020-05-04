<?php

namespace App\Http\Controller;

use App\Domain\Premium\Repository\PlanRepository;

class PlanController extends AbstractController
{

    public function plans (PlanRepository $planRepository) {
        return $planRepository->findall();
        return $plans;
    }

}
