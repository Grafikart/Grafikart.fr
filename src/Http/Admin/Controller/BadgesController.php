<?php

namespace App\Http\Admin\Controller;

use App\Domain\Badge\Entity\Badge;
use App\Http\Admin\Data\BadgeCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/badges', name: 'badge_')]
class BadgesController extends CrudController
{
    protected string $templatePath = 'badge';
    protected string $menuItem = 'badge';
    protected string $searchField = 'name';
    protected string $entity = Badge::class;
    protected string $routePrefix = 'admin_badge';
    protected array $events = [];

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return parent::crudIndex($this->getRepository()->createQueryBuilder('row'));
    }

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(): Response
    {
        $plan = new Badge();
        $data = new BadgeCrudData($plan);

        return $this->crudNew($data);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Badge $plan): Response
    {
        $data = new BadgeCrudData($plan);

        return $this->crudEdit($data);
    }

    #[Route(path: '/{id<\d+>}/clone', name: 'clone', methods: ['POST', 'GET'])]
    public function clone(Badge $plan): Response
    {
        $data = new BadgeCrudData(clone $plan);

        return $this->crudNew($data);
    }

    #[Route(path: '/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Badge $plan): Response
    {
        return $this->crudDelete($plan);
    }
}
