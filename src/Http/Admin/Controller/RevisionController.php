<?php

namespace App\Http\Admin\Controller;

use App\Domain\Blog\Event\PostCreatedEvent;
use App\Domain\Blog\Event\PostDeletedEvent;
use App\Domain\Blog\Event\PostUpdatedEvent;
use App\Domain\Revision\Revision;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Permet la gestion du blog
 */
final class RevisionController extends CrudController
{

    protected string $templatePath = 'revision';
    protected string $menuItem = 'revision';
    protected string $entity = Revision::class;
    protected string $routePrefix = 'admin_revision';
    protected array $events = [
        'update' => PostUpdatedEvent::class,
        'delete' => PostDeletedEvent::class,
        'create' => PostCreatedEvent::class
    ];

    /**
     * @Route("/revision/{id}", methods={"GET", "POST"}, name="revision_show")
     */
    public function edit(Revision $revision): Response
    {
        return $this->render('admin/revision/edit.html.twig', [
            'revision' => $revision
        ]);
    }

    /**
     * @Route("/revision/{id}", methods={"DELETE"})
     */
    public function delete(Revision $revision): Response
    {
        return $this->crudDelete($revision);
    }

}
