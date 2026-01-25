<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::query()
            ->where('online', true)
            ->orderByDesc('created_at')
            ->paginate(26);

        return view('courses.index', [
            'courses' => $courses,
            'page' => $request->integer('page', 1),
        ]);
    }

    public function show(string $slug, Course $course): View
    {
        return view('courses.show', [
            'course' => $course,
        ]);
    }
}
