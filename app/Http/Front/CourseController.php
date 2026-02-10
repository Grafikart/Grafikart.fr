<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Http\Front\Data\ContentFilterData;
use Illuminate\View\View;

class CourseController
{
    public function index(ContentFilterData $filter): View
    {
        $query = Course::query()
            ->published()
            ->with('mainTechnologies', 'formation')
            ->whereNull('formation_id')
            ->orderByDesc('created_at');

        if ($filter->technology) {
            $query->whereHas('mainTechnologies', fn ($q) => $q->where('slug', $filter->technology));
        }

        if ($filter->level) {
            $query->where('level', $filter->level);
        }

        if ($filter->duration) {
            $query->where('duration', '<=', $filter->duration * 60);
        }

        if ($filter->premium) {
            $query->where('premium', true);
        }

        $items = $query->paginate($filter->perPage())->withQueryString();

        return view('courses.index', [
            'items' => $items,
            'page' => $filter->page,
            'type' => 'course',
            'show_title' => ! $filter->isActive(),
        ]);
    }

    public function show(string $slug, Course $course): View
    {
        return view('courses.show', [
            'course' => $course,
        ]);
    }
}
