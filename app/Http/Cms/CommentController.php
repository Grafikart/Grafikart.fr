<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Comment\Comment;
use App\Http\Cms\Data\Comment\CommentRequestData;
use App\Http\Cms\Data\Comment\CommentRowData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class CommentController extends CmsController
{
    protected string $componentPath = 'comments';

    protected string $model = Comment::class;

    protected string $rowData = CommentRowData::class;

    protected string $formData = CommentRowData::class;

    protected string $requestData = CommentRequestData::class;

    protected string $route = 'comments';

    public function index(): Response
    {
        $query = Comment::query()
            ->with(['user', 'commentable'])
            ->orderBy('created_at', 'desc');

        return $this->cmsIndex(query: $query);
    }

    public function update(Comment $comment, CommentRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $comment, data: $data);
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        return $this->cmsDestroy($comment);
    }
}
