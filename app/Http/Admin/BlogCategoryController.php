<?php

namespace App\Http\Admin;

use App\Domains\Blog\BlogCategory;
use App\Domains\Cms\CmsController;
use App\Http\Admin\Data\Post\BlogCategoryData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class BlogCategoryController extends CmsController
{
    protected string $componentPath = 'blog/categories';
    protected string $model = BlogCategory::class;
    protected string $rowData = BlogCategoryData::class;
    protected string $formData = BlogCategoryData::class;
    protected string $requestData = BlogCategoryData::class;
    protected string $route = 'blog_category';

    public function index(): Response
    {
        return $this->cmsIndex();
    }

    public function edit(BlogCategory $blogCategory): Response
    {
        return $this->cmsEdit($blogCategory);
    }

    public function update(BlogCategory $blogCategory, BlogCategoryData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $blogCategory, data: $data);
    }

    public function create(): Response
    {
        return $this->cmsCreate();
    }

    public function store(BlogCategoryData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        return $this->cmsDestroy($blogCategory);
    }
}
