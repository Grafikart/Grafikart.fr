<?php

namespace App\Http\Admin\Controller;

use App\Domain\Comment\CommentRepository;
use App\Domain\Revision\RevisionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageController extends BaseController
{

    /**
     * @Route("", name="home")
     */
    public function index(
        RevisionRepository $revisionRepository,
        CommentRepository $commentRepository,
        Request $request
    ): Response {
        $revisions = $revisionRepository->findLatest();
        $comments = $commentRepository->paginateLatest($request->query->getInt('page', 1));
        return $this->render('admin/index.html.twig', [
            'revisions' => $revisions,
            'comments' => $comments
        ]);
    }

}
