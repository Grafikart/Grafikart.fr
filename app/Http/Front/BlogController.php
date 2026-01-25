<?php

namespace App\Http\Front;

use App\Domains\Blog\BlogCategory;
use App\Domains\Blog\Post;
use App\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Post::query()
            ->where('online', true)
            ->with(['attachment', 'category'])
            ->orderByDesc('created_at');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->get('category')));
        }

        $posts = $query->paginate(15)->withQueryString();

        $categories = BlogCategory::query()
            ->orderBy('name')
            ->get();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $request->get('category'),
        ]);
    }

    public function show(Post $post): View
    {
        return view('blog.show', [
            'post' => $post,
        ]);
    }
}
