<?php

namespace App\Http\Admin\Controller;

use App\Core\Helper\Paginator\PaginatorInterface;
use App\Domain\Comment\CommentRepository;
use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Notification\NotificationService;
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

    /**
     * @Route("/notify")
     */
    public function notify(NotificationService $notifications, CourseRepository $courseRepository): Response
    {
        $course = $courseRepository->find(103);
        $notification = $notifications->notifyChannel('content', 'Un nouveau tutoriel est disponible', $course);
        return new Response('ok');
    }

}
