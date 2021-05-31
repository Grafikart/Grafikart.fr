<?php

namespace App\Http\Admin\Controller;

use App\Domain\Podcast\Entity\Podcast;
use App\Http\Admin\Data\PodcastCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/podcast", name="podcast_")
 */
class PodcastController extends CrudController
{
    protected string $templatePath = 'podcast';
    protected string $menuItem = 'podcast';
    protected string $entity = Podcast::class;
    protected string $routePrefix = 'admin_podcast';
    protected string $searchField = 'name';

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
        $podcast = new Podcast();
        $data = new PodcastCrudData($podcast);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(Podcast $podcast): Response
    {
        return $this->crudDelete($podcast);
    }

    /**
     * @Route("/{id<\d+>}", name="edit")
     */
    public function edit(Podcast $podcast): Response
    {
        $data = new PodcastCrudData($podcast);

        return $this->crudEdit($data);
    }
}
