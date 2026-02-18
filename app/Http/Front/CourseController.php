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

    public function download(Course $course, string $type)
    {
        \Gate::authorize('download', $course);
        abort_if($type === 'source' && ! $course->source, 404, "Il n'y a pas de source pour ce tutoriel");

        if ($type === 'source') {
            return redirect($course->mediaUrl('source'));
        }

        return redirect($course->videoUrl());
    }
}
