<?php

namespace App\Http\Controller;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Revision\RevisionRepository;
use App\Domain\Revision\RevisionService;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Form\RevisionForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class RevisionController extends AbstractController
{
    /**
     * @Route("/revisions", name="revisions")
     * @IsGranted("ROLE_USER")
     */
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
     *
     * @Route("/revision/{id<\d+>}", name="revision")
     * @IsGranted(App\Http\Security\RevisionVoter::ADD)
     */
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
}
