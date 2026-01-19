<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Comment\Comment;
use App\Http\Cms\Data\Comment\CommentRequestData;
use App\Http\Cms\Data\Comment\CommentRowData;
use App\Infrastructure\Spam\SpamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CommentController extends CmsController
{
    protected string $componentPath = 'comments';

    protected string $model = Comment::class;

    protected string $rowData = CommentRowData::class;

    protected string $formData = CommentRowData::class;

    protected string $requestData = CommentRequestData::class;

    protected string $route = 'comments';

    public function index(Request $request, SpamService $spam): Response
    {
        $query = Comment::query()
            ->with(['user', 'commentable'])
            ->orderBy('created_at', 'desc');

        $filterSuspicious = $request->query->getBoolean('suspicious');
        if ($request->query('suspicious')) {
            $query->suspicious($spam->words());
        }

        return $this->cmsIndex(query: $query, extra: [
            'suspicious' => $filterSuspicious,
        ]);
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
