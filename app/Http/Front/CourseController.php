<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Http\Controller;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function show(string $slug, Course $course): View
    {
        return view('courses.show', [
            'course' => $course,
        ]);
    }
}
