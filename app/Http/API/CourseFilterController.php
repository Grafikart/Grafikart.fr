<?php

namespace App\Http\API;

use App\Domains\Course\Course;
use App\Domains\Course\DifficultyLevel;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;
use App\Http\API\Data\CourseFilter\CourseFilterItem;
use App\Http\API\Data\CourseFilter\CourseFiltersResponse;
use App\Http\API\Data\CourseFilter\CourseFilterTypeCount;

class CourseFilterController
{
    public function index(): CourseFiltersResponse
    {
        $scope = fn ($q) => $q->published(true);

        $technologies = Technology::query()
            ->withCount(['courses' => fn ($q) => $scope($q)->whereNull('formation_id'), 'formations' => $scope])
            ->whereHas('courses', $scope)
            ->orderByDesc('courses_count')
            ->get()
            ->map(fn (Technology $tech) => new CourseFilterItem(
                label: $tech->name,
                value: $tech->slug,
                courses_count: $tech->courses_count,
                formations_count: $tech->formations_count,
            ))
            ->all();

        $levels = array_map(fn (DifficultyLevel $level) => new CourseFilterItem(
            label: $level->label(),
            value: (string) $level->value,
        ), DifficultyLevel::cases());

        $courseCount = Course::query()->published()->whereNull('formation_id')->count();
        $formationCount = Formation::query()->where('online', true)->whereNull('deprecated_by_id')->count();

        return new CourseFiltersResponse(
            technologies: $technologies,
            levels: $levels,
            types: new CourseFilterTypeCount(
                course: $courseCount,
                formation: $formationCount,
            ),
        );
    }
}
