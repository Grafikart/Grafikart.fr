<?php

namespace App\Domains\History;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Path;
use App\Domains\Course\PathNode;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProgressionService
{
    public function trackProgress(User $user, Course $course, ?int $progress = null, ?int $score = null): Course
    {
        $data = array_filter([
            'progress' => $progress,
            'score' => $score,
        ], fn ($value) => $value !== null);

        Progress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'progressable_id' => $course->id,
                'progressable_type' => $course->getMorphClass(),
            ],
            $data,
        );

        // If a course is completed, and it belongs to a formation, update formation progress
        if ($progress === 1000 && $course->formation !== null) {
            $this->updateFormationProgress($user, $course->formation);
        }

        return $course;
    }

    /**
     * Return the progress of the user on a specific content as a tuple [completed, total]
     *
     * @return int[]
     */
    public function progress(Formation $formation, ?User $user): array
    {
        $courseIds = $formation->courseIds;
        $totalCourses = $courseIds->count();

        if ($totalCourses === 0 || ! $user) {
            return [0, $totalCourses];
        }

        $completedCount = Progress::query()
            ->where('user_id', $user->id)
            ->completed()
            ->where('progressable_type', (new Course)->getMorphClass())
            ->whereIn('progressable_id', $courseIds)
            ->count();

        return [$completedCount, $totalCourses];
    }

    /**
     * Return the completed node in a cursus
     *
     * @return int[]
     */
    public function completedNodeIds(Path $path): array
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return [];
        }

        /** @var Collection<int, PathNode> $nodes */
        $nodes = $path->nodes()
            ->whereNotNull('content_id')
            ->select('content_id', 'content_type')
            ->get();
        $contentIds = $nodes->pluck('content_id');

        if ($contentIds->isEmpty()) {
            return [];
        }

        /** @var Collection $completedItems */
        $completedItems = Progress::query()
            ->completed()
            ->where('user_id', $user->id)
            ->whereIn('progressable_id', $contentIds)
            ->get(['progressable_type', 'progressable_id'])
            ->map(fn (Progress $p): string => sprintf('%s:%s', $p->progressable_type, $p->progressable_id));

        return $nodes
            ->filter(fn (PathNode $node) => $completedItems->contains(sprintf('%s:%s', $node->content_type, $node->content_id)))
            ->pluck('id')
            ->values()
            ->all();
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

    /**
     * Return the progression linked to the collection
     */
    public function forCollection(?User $user, ?Collection $collection, $minCompleted = 0): Collection
    {
        if (! $user || ! $collection) {
            return collect();
        }
        $first = $collection->first();
        if (! $first) {
            return collect();
        }

        $type = $first instanceof Model ? $first->getMorphClass() : 'course';
        $ids = $collection->map(function ($item) {
            if (is_int($item)) {
                return $item;
            }
            if (is_string($item)) {
                return intval($item);
            }
            assert($item instanceof Model);

            return $item->getKey();
        });

        return Progress::query()
            ->where('progressable_type', $type)
            ->where('user_id', $user->id)
            ->where('progress', '>=', $minCompleted)
            ->whereIn('progressable_id', $ids)
            ->pluck('progress', 'progressable_id');
    }

    public function completedForCollection(?User $user, ?Collection $collection): Collection
    {
        return $this->forCollection($user, $collection, 1000)->keys();
    }

    /**
     * Retrieve the progression for the User
     */
    public function findProgress(?User $user, Model $model): ?Progress
    {
        if (! $user) {
            return null;
        }

        return Progress::query()
            ->where('user_id', $user->id)
            ->where('progressable_type', $model->getMorphClass())
            ->where('progressable_id', $model->getKey())
            ->first();
    }
}
