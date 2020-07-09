<?php

namespace App\Http\Admin\Controller;

use App\Domain\Blog\Event\PostCreatedEvent;
use App\Domain\Blog\Event\PostDeletedEvent;
use App\Domain\Blog\Event\PostUpdatedEvent;
use App\Domain\Revision\Event\RevisionAcceptedEvent;
use App\Domain\Revision\Event\RevisionRefusedEvent;
use App\Domain\Revision\Revision;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Permet la gestion du blog.
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
        'create' => PostCreatedEvent::class,
    ];

    /**
     * @Route("/revision/{id}", methods={"GET", "POST"}, name="revision_show")
     */
    public function edit(Revision $revision, Request $request, EventDispatcherInterface $dispatcher): Response
    {
        if ('POST' === $request->getMethod()) {
            $isDeleteRequest = null !== $request->get('delete');
            if ($isDeleteRequest) {
                $dispatcher->dispatch(new RevisionRefusedEvent($revision));
                $this->addFlash('success', 'La révision a bien été supprimée');
            } else {
                $revision->setContent($request->get('content'));
                $dispatcher->dispatch(new RevisionAcceptedEvent($revision));
                $this->addFlash('success', 'La révision a bien été acceptée');
            }

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/revision/edit.html.twig', [
            'revision' => $revision,
        ]);
    }

    /**
     * @Route("/revision/{id}", methods={"DELETE"})
     */
    public function delete(Revision $revision, EventDispatcherInterface $dispatcher): Response
    {
        $dispatcher->dispatch(new RevisionRefusedEvent($revision));
        $this->addFlash('success', 'La révision a bien été supprimée');

        return $this->redirectToRoute('admin_home');
    }
}
