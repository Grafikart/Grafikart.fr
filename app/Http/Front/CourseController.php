<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Http\Controller;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()
            ->where('online', true)
            ->with(['technologies' => fn ($q) => $q->wherePivot('primary', true)])
            ->orderByDesc('created_at')
            ->paginate(26);

        return view('courses.index', [
            'courses' => $courses,
        ]);
    }

    public function show(string $slug, Course $course): View
    {
        return view('courses.show', [
            'course' => $course,
        ]);
    }
}
