<?php

namespace App\Http\Front;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Path;

class HomeController
{
    public function index()
    {
        $formations = Formation::latest()
            ->published()
            ->with('technologies')
            ->limit(3);
        $courses = Course::latest()
            ->published()
            ->with('technologies')
            ->limit(3);
        $posts = Post::latest()
            ->published()
            ->limit(5);
        $paths = Path::latest();

        return view('pages.home', [
            'queries' => [
                'formations' => $formations,
                'courses' => $courses,
                'posts' => $posts,
                'paths' => $paths,
            ],
        ]);
    }
}
