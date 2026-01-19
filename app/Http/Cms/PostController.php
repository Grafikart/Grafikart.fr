<?php

namespace App\Http\Cms;

use App\Domains\Blog\BlogCategory;
use App\Domains\Blog\Post;
use App\Domains\Cms\CmsController;
use App\Http\Cms\Data\Blog\PostFormData;
use App\Http\Cms\Data\Blog\PostRequestData;
use App\Http\Cms\Data\Blog\PostRowData;
use App\Http\Cms\Data\OptionItemData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class PostController extends CmsController
{
    protected string $componentPath = 'blog';

    protected string $model = Post::class;

    protected string $rowData = PostRowData::class;

    protected string $formData = PostFormData::class;

    protected string $requestData = PostRequestData::class;

    protected string $route = 'posts';

    public function index(Request $request): Response
    {
        $query = Post::query()
            ->with(['category'])
            ->orderByDesc('created_at');

        if ($request->has('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->has('q')) {
            $search = $request->string('q');
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $this->cmsIndex(query: $query);
    }

    public function create(): Response
    {

        return $this->cmsCreate(extra: [
            'categories' => $this->getCategories(),
        ]);
    }

    public function store(PostRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(Post $post): Response
    {
        $post->load(['category', 'attachment']);

        return $this->cmsEdit($post, [
            'categories' => $this->getCategories(),
        ]);
    }

    public function update(Post $post, PostRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $post, data: $data);
    }

    public function destroy(Post $post): RedirectResponse
    {
        return $this->cmsDestroy($post, "L'article {$post->title} a été supprimé");
    }

    public function getCategories(): array
    {
        return OptionItemData::collect(BlogCategory::query()
            ->orderBy('name')
            ->get()
        )->toArray();
    }
}
