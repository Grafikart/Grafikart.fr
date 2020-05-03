<?php

namespace App\Http\Admin\Controller;

use App\Domain\Premium\Entity\Plan;
use App\Http\Admin\Data\CrudPlanData;
use App\Infrastructure\Payment\Paypal\PaypalApi;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan")
 */
class PlanController extends CrudController
{

    protected string $templatePath = 'plan';
    protected string $menuItem = 'plan';
    protected string $entity = Plan::class;
    protected string $routePrefix = 'admin_plan';
    protected array $events = [];

    /**
     * @Route("/new", name="plan_new", methods={"POST", "GET"})
     */
    public function new(): Response
    {
        $plan = new Plan();
        $data = new CrudPlanData($plan);
        return $this->crudNew($data);
    }

    /**
     * @Route("/{id}", name="plan_edit", methods={"POST", "GET"})
     */
    public function edit(Plan $plan): Response
    {
        $data = new CrudPlanData($plan);
        return $this->crudEdit($data);
    }

    /**
     * @Route("/{id}/clone", name="plan_clone", methods={"POST", "GET"})
     */
    public function clone(Plan $plan): Response
    {
        $data = new CrudPlanData(clone $plan);
        return $this->crudNew($data);
    }

    /**
     * @Route("/{id}/paypal", name="plan_paypal", methods={"POST", "GET"})
     */
    public function paypal(Plan $plan, PaypalApi $api): Response
    {
        $api->syncPlan($plan);
        $this->em->flush();
        return $this->redirectToRoute('admin_plan_edit', ['id' => $plan->getId()]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(Plan $plan) {
        return $this->crudDelete($plan, 'admin_premium');
    }

}
