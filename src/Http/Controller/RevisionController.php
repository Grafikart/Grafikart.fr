<?php

namespace App\Http\Controller;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Revision\Revision;
use App\Domain\Revision\RevisionRepository;
use App\Domain\Revision\RevisionService;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Form\RevisionForm;
use App\Http\Security\RevisionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User getUser()
 */
class RevisionController extends AbstractController
{
    #[Route(path: '/revisions', name: 'revisions')]
    #[IsGranted('ROLE_USER')]
    public function index(
        RevisionRepository $repository,
        PaginatorInterface $paginator
    ): Response {
        $query = $repository->queryAllForUser($this->getUserOrThrow());
        $revisions = $paginator->paginate($query->getQuery());

        return $this->render('account/revisions.html.twig', [
            'revisions' => $revisions,
            'menu' => 'account',
        ]);
    }

    /**
     * Affiche la page qui permet la soumission d'une révision.
     * Pour ce endpoint on ne passe pas l'ID de la révision mais l'id du contenu à modifier.
     */
    #[Route(path: '/revision/{id<\d+>}', name: 'revision', methods: ['GET', 'POST'])]
    #[IsGranted(RevisionVoter::ADD, subject: 'content')]
    public function show(Content $content, Request $request, RevisionService $service): Response
    {
        $revision = $service->revisionFor($this->getUser(), $content);
        $form = $this->createForm(RevisionForm::class, $revision);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->flashErrors($form);
            } else {
                $service->submitRevision($revision);
                $this->addFlash(
                    'success',
                    "Votre modification a bien été enregistrée, vous pouvez revenir sur vos changements tant qu'ils n'ont pas été validés"
                );
            }
        }

        return $this->render('content/revision.html.twig', [
            'revision' => $revision,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime une révision.
     */
    #[Route(path: '/revision/{id<\d+>}', methods: ['DELETE'])]
    #[IsGranted(RevisionVoter::DELETE, subject: 'revision')]
    public function delete(Revision $revision, EntityManagerInterface $em): Response
    {
        $em->remove($revision);
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
