<?php

namespace App\Http\API;

use App\Domains\Course\Course;
use App\Http\Cms\Data\Question\QuestionData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class QuestionController
{
    public function index(Course $course, Request $request): Collection
    {
        $user = $request->user();
        assert($user instanceof User);
        Gate::authorize('quiz', $course);

        return QuestionData::collect($course->questions);
    }
}
