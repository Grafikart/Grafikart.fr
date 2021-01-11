<?php

namespace App\Http\Admin\Controller;

use App\Domain\Course\Entity\CursusCategory;
use App\Domain\Course\Repository\CursusCategoryRepository;
use App\Http\Admin\Data\CursusCategoryCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cursusc", name="cursuscategory_")
 */
class CursusCategoryController extends CrudController
{
    protected string $templatePath = 'cursuscategory';
    protected string $menuItem = 'cursuscategory';
    protected string $entity = CursusCategory::class;
    protected string $routePrefix = 'admin_cursuscategory';
    protected string $searchField = 'name';

    /**
     * @Route("/", name="index")
     */
    public function index(CursusCategoryRepository $repository): Response
    {
        $q = $repository->createQueryBuilder('c');

        return $this->crudIndex($q);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(): Response
    {
        $cursuscategory = new CursusCategory();
        $data = new CursusCategoryCrudData($cursuscategory);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(CursusCategory $cursuscategory): Response
    {
        return $this->crudDelete($cursuscategory);
    }

    /**
     * @Route("/{id<\d+>}", name="edit")
     */
    public function edit(CursusCategory $cursuscategory): Response
    {
        $data = new CursusCategoryCrudData($cursuscategory);

        return $this->crudEdit($data);
    }
}
