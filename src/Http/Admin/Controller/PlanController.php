<?php

namespace App\Http\Admin\Controller;

use App\Domain\Premium\Entity\Plan;
use App\Http\Admin\Data\Plan\PlanItemData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @method getRepository() \App\Domain\Premium\Repository\PlanRepository
 */
#[Route(path: '/plans', name: 'plan_')]
final class PlanController extends InertiaController
{
    protected string $entityClass = Plan::class;
    protected string $routePrefix = 'plan';
    protected string $componentDirectory = 'plans';
    protected string $itemDataClass = PlanItemData::class;

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.id', 'DESC');

        return $this->crudIndex($query);
    }
}
