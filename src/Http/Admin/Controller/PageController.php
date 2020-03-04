<?php

namespace App\Http\Admin\Controller;

use App\Domain\Revision\RevisionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageController extends BaseController
{

    /**
     * @Route("", name="index")
     */
    public function index(RevisionRepository $revisionRepository): Response
    {
        $revisions = $revisionRepository->findLatest();
        return $this->render('admin/index.html.twig', [
            'revisions' => $revisions
        ]);
    }

}
