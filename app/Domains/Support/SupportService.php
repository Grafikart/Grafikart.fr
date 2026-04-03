<?php

namespace App\Domains\Support;

use App\Domains\Course\Course;
use App\Http\API\Data\SupportQuestionRequestData;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;

class SupportService
{
    /**
     * @return Collection<int, SupportQuestion>
     */
    public function questionsFor(Course $course, ?User $user): Collection
    {
        $query = SupportQuestion::query()
            ->where('course_id', $course->id)
            ->orderByDesc('id');

        if ($user) {
            $query->where(fn ($query) => $query->where('online', true)->orWhere('user_id', $user->id));
        } else {
            $query->where('online', true);
        }

        return $query->get();
    }

    /**
     * @throws AuthorizationException
     */
    public function createQuestion(Course $course, User $user, SupportQuestionRequestData $data): SupportQuestion
    {
        $this->ensureCanAccessCourseSupport($course, $user);

        $question = SupportQuestion::query()->create([
            'user_id' => $user->id,
            'title' => $data->title,
            'content' => trim($data->content ?? ''),
            'answer' => null,
            'online' => false,
            'course_id' => $course->id,
            'timestamp' => $data->timestamp,
        ]);

        return $question->loadMissing([
            'user:id,name',
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    private function ensureCanAccessCourseSupport(Course $course, ?User $user): void
    {
        if ($course->isPublic()) {
            return;
        }

        if ($user?->can('watch', $course)) {
            return;
        }

        throw new AuthorizationException;
    }
}
