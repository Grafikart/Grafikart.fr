<?php

namespace App\Http\Admin\Controller;

use App\Domain\Comment\CommentRepository;
use App\Domain\Revision\RevisionRepository;
use App\Helper\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageController extends BaseController
{

    /**
     * @Route("", name="home")
     */
    public function index(
        PaginatorInterface $paginator,
        RevisionRepository $revisionRepository,
        CommentRepository $commentRepository,
        Request $request
    ): Response {
        $revisions = $revisionRepository->findLatest();
        $comments = $paginator->paginate($commentRepository->queryLatest());
        return $this->render('admin/index.html.twig', [
            'revisions' => $revisions,
            'comments' => $comments
        ]);
    }

}
