<?php

namespace App\Http\Front;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Http\Controller;
use Illuminate\Http\Response;

/**
 * RSS feed
 */
class FeedController extends Controller
{
    /**
     * Retrieve the last Course, Blog post and Formation for the RSS feed
     */
    public function index(): Response
    {
        $fields = ['slug', 'id', 'title', 'content', 'created_at'];
        $courses = Course::published()->select($fields)->latest()->limit(10)->get();
        // posts & formations are less frequent than courses
        $posts = Post::published()->select($fields)->latest()->limit(3)->get();
        $formations = Formation::published()->select($fields)->latest()->limit(3)->get();

        // Merge the collection to get the latest published content
        $items = $courses->concat($posts)->concat($formations)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return response()
            ->view('feed.rss', ['items' => $items])
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
