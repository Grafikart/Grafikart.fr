<?php

namespace App\Http\Admin\Controller;

use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Http\Admin\Data\TechnologyCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/technology", name="technology_")
 */
class TechnologyController extends CrudController
{

    protected string $templatePath = 'technology';
    protected string $menuItem = 'technology';
    protected string $entity = Technology::class;
    protected string $routePrefix = 'admin_technology';
    protected string $searchField = 'name';

    /**
     * @Route("/", name="index")
     */
    public function index(TechnologyRepository $repository): Response
    {
        $this->paginator->allowSort('count', 'row.id', 'row.name');
        $query = $repository
            ->createQueryBuilder('row')
            ->leftJoin('row.usages', 'usage')
            ->groupBy('row.id')
            ->orderBy('row.id', 'DESC')
            ->addSelect('COUNT(usage.technology) as count')
        ;
        return $this->crudIndex($query);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(TechnologyRepository $repository): Response
    {
        $technology = new Technology();
        $data = new TechnologyCrudData($technology);
        return $this->crudNew($data);
    }

    /**
     * @Route("/{id}", name="edit")
     */
    public function edit(Technology $technology): Response
    {
        $data = new TechnologyCrudData($technology);
        return $this->crudEdit($data);
    }

}
