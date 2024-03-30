<?php

namespace App\Http\Admin\Controller;

use App\Domain\Badge\Entity\Badge;
use App\Domain\School\Entity\School;
use App\Domain\School\Repository\SchoolRepository;
use App\Http\Admin\Data\BadgeCrudData;
use App\Http\Admin\Data\SchoolCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/schools', name: 'school_')]
class SchoolsController extends CrudController
{
    protected string $templatePath = 'school';
    protected string $menuItem = 'school';
    protected string $searchField = 'name';
    protected string $entity = School::class;
    protected string $routePrefix = 'admin_school';
    protected array $events = [];

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return parent::crudIndex($this->getRepository()->createQueryBuilder('row'));
    }

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(): Response
    {
        $school = new School();
        $data = new SchoolCrudData($school);

        return $this->crudNew($data);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(School $school, SchoolRepository $repository): Response
    {
        $data = new SchoolCrudData($school);

        return $this->crudEdit($data, [
            'students' => $school->getStudents()
        ]);
    }

    #[Route(path: '/{id<\d+>}', methods: ['DELETE'])]
    public function delete(School $plan): Response
    {
        return $this->crudDelete($plan);
    }
}
