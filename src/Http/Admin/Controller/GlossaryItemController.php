<?php

namespace App\Http\Admin\Controller;

use App\Domain\Glossary\Entity\GlossaryItem;
use App\Http\Admin\Data\GlossaryItemCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lexique", name="glossary_")
 */
class GlossaryItemController extends CrudController
{
    protected string $templatePath = 'glossary';
    protected string $menuItem = 'glossary';
    protected string $entity = GlossaryItem::class;
    protected string $routePrefix = 'admin_glossary';
    protected string $searchField = 'name';
    protected string $redirectRoute = 'index';

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->crudIndex();
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(): Response
    {
        $lexique = new GlossaryItem();
        $data = new GlossaryItemCrudData($lexique);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(GlossaryItem $lexique): Response
    {
        return $this->crudDelete($lexique);
    }

    /**
     * @Route("/{id<\d+>}", name="edit")
     */
    public function edit(GlossaryItem $lexique): Response
    {
        $data = new GlossaryItemCrudData($lexique);

        return $this->crudEdit($data);
    }
}
