<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\History\Progress;
use App\Domains\History\ProgressionService;
use App\Helpers\UrlGenerator;
use App\Http\Front\Data\ContentFilterData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormationController
{
    public function index(Request $request, ContentFilterData $filter, ProgressionService $progressionService): View
    {
        $query = Formation::query()
            ->where('online', true)
            ->whereNull('deprecated_by_id')
            ->with('mainTechnologies', 'courses')
            ->orderByDesc('created_at');

        if ($filter->technology) {
            $query->whereHas('mainTechnologies', fn ($q) => $q->where('slug', $filter->technology));
        }

        $items = $query->paginate($filter->perPage())->withQueryString();

        return view('courses.index', [
            'items' => $items,
            'page' => $filter->page,
            'type' => 'formation',
            'progress' => $progressionService->forCollection($request->user(), $items->getCollection()),
            'show_title' => ! $filter->isActive(),
        ]);
    }

    public function show(Formation $formation, Request $request, ProgressionService $progressService): View|RedirectResponse
    {
        if ($formation->force_redirect && $formation->deprecatedBy) {
            return redirect(app_url($formation->deprecatedBy), 301);
        }

        return view('courses.formation', [
            'formation' => $formation,
            'completed' => $progressService->completedForCollection($request->user(), $formation->course_ids),
            'total' => $formation->course_ids->count(),
        ]);
    }

    /**
     * Redirect to the first "not completed" course of a Formation
     */
    public function continue(Formation $formation, Request $request, UrlGenerator $urlGenerator): RedirectResponse
    {
        $courseIds = $formation->courseIds;
        $firstCourseId = $courseIds->first();

        abort_if($firstCourseId === null, 404);

        $nextCourseId = $firstCourseId;
        $user = $request->user();

        if ($user) {
            $completedCourseIds = Progress::query()
                ->completed()
                ->where('user_id', $user->id)
                ->where('progressable_type', (new Course)->getMorphClass())
                ->whereIn('progressable_id', $courseIds)
                ->pluck('progressable_id');

            $nextCourseId = $courseIds->first(
                fn (int $courseId) => ! $completedCourseIds->contains($courseId)
            ) ?? $firstCourseId;
        }

        $course = Course::query()
            ->select('slug', 'id')
            ->findOrFail($nextCourseId);

        return redirect()->to($urlGenerator->url($course));
    }
}
