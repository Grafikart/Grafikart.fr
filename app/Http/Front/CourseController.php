<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Domains\History\ProgressionService;
use App\Http\Front\Data\ContentFilterData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController
{
    public function index(\Illuminate\Http\Request $request, ContentFilterData $filter, ProgressionService $progressionService): View
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
        $progress = $progressionService->forCollection($request->user(), $items->getCollection());

        return view('courses.index', [
            'items' => $items,
            'page' => $filter->page,
            'type' => 'course',
            'progress' => $progress,
            'show_title' => ! $filter->isActive(),
        ]);
    }

    public function show(string $slug, Course $course, Request $request, ProgressionService $progressionService): View
    {
        $progress = $progressionService->findProgress($request->user(), $course);

        return view('courses.show', [
            'course' => $course,
            'completed' => $progressionService->completedForCollection($request->user(), $course->formation?->course_ids),
            'start' => $progress ? round($progress->ratio * $course->duration) : 0,
            'quizCompleted' => $progress?->score !== null,
        ]);
    }

    public function download(Course $course, string $type): RedirectResponse
    {
        \Gate::authorize('download', $course);
        abort_if($type === 'source' && ! $course->source, 404, "Il n'y a pas de source pour ce tutoriel");

        if ($type === 'source') {
            return redirect($course->mediaUrl('source'));
        }

        return redirect($course->videoUrl());
    }
}
