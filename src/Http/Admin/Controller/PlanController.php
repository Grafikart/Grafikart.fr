<?php

namespace App\Http\Admin\Controller;

use App\Domain\Premium\Entity\Plan;
use App\Http\Admin\Data\CrudPlanData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/plan', name: 'plan_')]
class PlanController extends CrudController
{
    protected string $templatePath = 'plan';
    protected string $menuItem = 'plan';
    protected string $entity = Plan::class;
    protected string $routePrefix = 'admin_plan';
    protected array $events = [];

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(): Response
    {
        $plan = new Plan();
        $data = new CrudPlanData($plan);

        return $this->crudNew($data);
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $query = $this->getRepository()->createQueryBuilder('row');

        return $this->crudIndex($query);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Plan $plan): Response
    {
        $data = new CrudPlanData($plan);

        return $this->crudEdit($data);
    }

    #[Route(path: '/{id<\d+>}/clone', name: 'clone', methods: ['POST', 'GET'])]
    public function clone(Plan $plan): Response
    {
        $data = new CrudPlanData(clone $plan);

        return $this->crudNew($data);
    }

    #[Route(path: '/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Plan $plan): Response
    {
        return $this->crudDelete($plan, 'admin_premium');
    }
}
