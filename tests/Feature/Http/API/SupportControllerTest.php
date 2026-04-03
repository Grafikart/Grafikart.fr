<?php

use App\Domains\Course\Course;
use App\Domains\Support\SupportQuestion;

test('guests only receive published', function () {
    $course = Course::factory()->create();
    $publishedQuestion = SupportQuestion::factory()->for($course)->online()->create([
        'answer' => '**Réponse** avec [lien](https://grafikart.fr)',
    ]);
    SupportQuestion::factory()->for($course)->offline()->create([
        'answer' => 'Réponse privée',
    ]);

    $this->getJson("/api/courses/{$course->id}/support")
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $publishedQuestion->id);
});
