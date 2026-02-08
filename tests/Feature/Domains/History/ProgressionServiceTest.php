<?php

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\History\Progress;
use App\Domains\History\ProgressionService;
use App\Models\User;

beforeEach(function () {
    $this->service = new ProgressionService;
    $this->user = User::factory()->create();
});

describe('trackProgress', function () {
    it('creates a new progress record for a course', function () {
        $course = Course::factory()->create();

        $this->service->trackProgress($this->user, $course, 500);

        expect(Progress::query()->count())->toBe(1);
        $progress = Progress::query()->first();
        expect($progress->user_id)->toBe($this->user->id)
            ->and($progress->progressable_id)->toBe($course->id)
            ->and($progress->progressable_type)->toBe($course->getMorphClass())
            ->and($progress->progress)->toBe(500);
    });

    it('updates existing progress for a course', function () {
        $course = Course::factory()->create();

        $this->service->trackProgress($this->user, $course, 300);
        $this->service->trackProgress($this->user, $course, 700);

        expect(Progress::query()->count())->toBe(1);
        $progress = Progress::query()->first();
        expect($progress->progress)->toBe(700);
    });

    it('returns the course model', function () {
        $course = Course::factory()->create();

        $result = $this->service->trackProgress($this->user, $course, 500);

        expect($result)->toBeInstanceOf(Course::class)
            ->and($result->id)->toBe($course->id);
    });

    it('handles multiple users tracking the same course', function () {
        $course = Course::factory()->create();
        $user2 = User::factory()->create();

        $this->service->trackProgress($this->user, $course, 300);
        $this->service->trackProgress($user2, $course, 700);

        expect(Progress::query()->count())->toBe(2);
        expect(Progress::query()->where('user_id', $this->user->id)->first()->progress)->toBe(300);
        expect(Progress::query()->where('user_id', $user2->id)->first()->progress)->toBe(700);
    });
});

describe('formation progress tracking', function () {
    it('does not update formation progress when course is not completed', function () {
        $formation = Formation::factory()->create();
        $course = Course::factory()->create(['formation_id' => $formation->id]);

        $this->service->trackProgress($this->user, $course, 500);

        expect(Progress::query()->where('progressable_type', $formation->getMorphClass())->count())->toBe(0);
    });

    it('updates formation progress when course is completed', function () {
        $formation = Formation::factory()->withChapters(1, 1)->create();
        $course = $formation->courses->first();

        $this->service->trackProgress($this->user, $course, 1000);

        $formationProgress = Progress::query()
            ->where('user_id', $this->user->id)
            ->where('progressable_id', $formation->id)
            ->where('progressable_type', $formation->getMorphClass())
            ->first();

        expect($formationProgress)->not->toBeNull()
            ->and($formationProgress->progress)->toBe(1000);
    });

    it('calculates formation progress correctly with multiple courses', function () {
        $formation = Formation::factory()->withChapters(1, 4)->create();
        [$course1, $course2, $course3, $course4] = $formation->courses;

        // Complete 2 out of 4 courses (50%)
        $this->service->trackProgress($this->user, $course1, 1000);
        $this->service->trackProgress($this->user, $course2, 1000);

        $formationProgress = Progress::query()
            ->where('user_id', $this->user->id)
            ->where('progressable_id', $formation->id)
            ->where('progressable_type', $formation->getMorphClass())
            ->first();

        expect($formationProgress->progress)->toBe(500);

        // Complete one more course (75%)
        $this->service->trackProgress($this->user, $course3, 1000);

        $formationProgress->refresh();
        expect($formationProgress->progress)->toBe(750);

        // Complete the last course (100%)
        $this->service->trackProgress($this->user, $course4, 1000);

        $formationProgress->refresh();
        expect($formationProgress->progress)->toBe(1000);
    });

    it('does not affect other users formation progress', function () {
        $formation = Formation::factory()->withChapters(1, 1)->create();
        $course = $formation->courses->first();
        $user2 = User::factory()->create();

        $this->service->trackProgress($this->user, $course, 1000);

        $user2FormationProgress = Progress::query()
            ->where('user_id', $user2->id)
            ->where('progressable_id', $formation->id)
            ->where('progressable_type', $formation->getMorphClass())
            ->first();

        expect($user2FormationProgress)->toBeNull();
    });

    it('handles courses without formations gracefully', function () {
        $course = Course::factory()->create(['formation_id' => null]);

        $this->service->trackProgress($this->user, $course, 1000);

        expect(Progress::query()->count())->toBe(1);
        $progress = Progress::query()->first();
        expect($progress->progressable_type)->toBe($course->getMorphClass());
    });

    it('updates formation progress when completing partial progress', function () {
        $formation = Formation::factory()->withChapters(1, 1)->create();
        $course = $formation->courses->first();

        // Track partial progress first
        $this->service->trackProgress($this->user, $course, 500);
        expect(Progress::query()->where('progressable_type', $formation->getMorphClass())->count())->toBe(0);

        // Complete the course
        $this->service->trackProgress($this->user, $course, 1000);

        $formationProgress = Progress::query()
            ->where('progressable_id', $formation->id)
            ->where('progressable_type', $formation->getMorphClass())
            ->first();

        expect($formationProgress)->not->toBeNull()
            ->and($formationProgress->progress)->toBe(1000);
    });
});
