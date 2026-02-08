<?php

namespace App\Domains\History;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Models\User;

class ProgressionService
{
    public function trackProgress(User $user, Course $course, int $progress): Course
    {
        Progress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'progressable_id' => $course->id,
                'progressable_type' => $course->getMorphClass(),
            ],
            [
                'progress' => $progress,
            ]
        );

        // If a course is completed, and it belongs to a formation, update formation progress
        if ($progress === 1000 && $course->formation !== null) {
            $this->updateFormationProgress($user, $course->formation);
        }

        return $course;
    }

    /**
     * Update the progression for a formation
     */
    private function updateFormationProgress(User $user, Formation $formation): void
    {
        $courseIds = $formation->courseIds;
        $totalCourses = count($courseIds);

        if ($totalCourses === 0) {
            return;
        }

        // Count completed courses
        $completedCount = Progress::query()
            ->where('user_id', $user->id)
            ->where('progressable_type', (new Course)->getMorphClass())
            ->whereIn('progressable_id', $courseIds)
            ->where('progress', 1000)
            ->count();

        // Calculate formation progress
        $formationProgress = (int) round(($completedCount / $totalCourses) * 1000);

        // Update or create formation progress
        Progress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'progressable_id' => $formation->id,
                'progressable_type' => $formation->getMorphClass(),
            ],
            [
                'progress' => $formationProgress,
            ]
        );
    }
}
