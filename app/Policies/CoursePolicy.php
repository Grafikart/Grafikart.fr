<?php

namespace App\Policies;

use App\Domains\Course\Course;
use App\Models\User;

class CoursePolicy
{
    public function watch(User $user, Course $course): bool
    {
        if ($course->isPremium() || $course->isScheduled()) {
            return $user->isPremium();
        }

        return true;
    }

    public function quiz(User $user, Course $course): bool
    {
        return $user->isPremium();
    }
}
