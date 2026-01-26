<?php

namespace App\Http\Front;

use App\Domains\Blog\BlogCategory;
use App\Domains\Blog\Post;
use App\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(BlogCategory $category, Request $request): View
    {
        $query = Post::query()
            ->where('online', true)
            ->with(['attachment', 'category'])
            ->orderByDesc('created_at');

        if ($category->exists) {
            $query->where('category_id', $category->id);
        }

        $posts = $query->paginate(10);

        $categories = BlogCategory::query()
            ->withCount(['posts' => fn ($query) => $query->where('online', true)])
            ->orderBy('name')
            ->get();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'category' => $category->exists ? $category : null,
            'page' => $request->integer('page'),
        ]);
    }

    public function show(Post $post): View
    {
        return view('blog.show', [
            'post' => $post,
        ]);
    }
}
