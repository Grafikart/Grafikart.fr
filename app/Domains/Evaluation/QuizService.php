<?php

namespace App\Domains\Evaluation;

use App\Domains\Course\Course;
use App\Models\User;

class QuizService
{
    public function isCompleted(Course $course, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $course->progress()
            ->where('user_id', $user->id)
            ->whereNotNull('score')
            ->exists();
    }
}
