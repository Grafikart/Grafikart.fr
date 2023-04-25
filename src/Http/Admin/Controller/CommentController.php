<?php

namespace App\Http\Admin\Controller;

use App\Domain\Comment\CommentRepository;
use App\Domain\Comment\Entity\Comment;
use App\Http\Admin\Data\CommentCrudData;
use App\Infrastructure\Spam\SpamService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/comment", name:"comment_")]
class CommentController extends CrudController
{
    protected string $templatePath = 'comment';
    protected string $menuItem = 'comments';
    protected string $entity = Comment::class;
    protected string $routePrefix = 'admin_comment';
    protected string $searchField = 'name';

    #[Route(path: "/", name:"index")]
    public function index(Request $request, SpamService $spamService): Response
    {
        $repository = $this->getRepository();
        $query = null;
        $suspiciousFilter = $request->get('suspicious');
        if ($suspiciousFilter && $repository instanceof CommentRepository) {
            $query = $repository->querySuspicious($spamService->words());
        }
        return $this->crudIndex($query, [
            'suspicious_filter' => $suspiciousFilter
        ]);
    }

    #[Route(path: "/{id<\d+>}", name:"delete", methods:["DELETE"])]
    public function delete(Comment $comment): Response
    {
        return $this->crudDelete($comment);
    }

    #[Route(path: "/{id<\d+>}", name:"edit")]
    public function edit(Comment $comment): Response
    {
        $data = new CommentCrudData($comment);

        return $this->crudEdit($data);
    }
}
