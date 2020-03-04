<?php

namespace App\Http\Controller;

use App\Domain\Application\Entity\Content;
use App\Domain\Revision\RevisionService;
use App\Http\Form\RevisionForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RevisionController extends AbstractController
{

    /**
     * Affiche la page qui permet la soumission d'une révision
     *
     * @Route("/revision/{id}", name="revision")
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
                $this->addFlash('success',
                    "Votre modification a bien été enregistrée, vous pouvez revenir sur vos changements tant qu'ils n'ont pas été validés");
            }
        }
        return $this->render('content/revision.html.twig', [
            'revision' => $revision,
            'form'     => $form->createView()
        ]);
    }

}
