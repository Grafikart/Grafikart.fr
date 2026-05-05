<?php

namespace App\Http\Front;

use App\Domains\Course\DifficultyLevel;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;

class TechnologyController
{
    public function show(Technology $technology)
    {
        $formations = $technology->formations()
            ->published()
            ->orderBy('level')
            ->get()
            ->groupBy(fn (Formation $formation) => $formation->level === DifficultyLevel::Junior ? 0 : 1);
        $courses = $technology->courses()->published()->whereNull('formation_id')->limit(12)->paginate();
        $dependents = $technology->dependents()->whereNull('deprecated_by_id')->get()->groupBy('type');

        return view('courses.technology', [
            'technology' => $technology,
            'formations' => $formations,
            'courses' => $courses,
            'isEmpty' => $courses->isEmpty() && $formations->isEmpty(),
            'dependents' => $dependents,
            'requirements' => $technology->requirements,
        ]);
    }
}
