<?php

use App\Domains\Course\Course;

beforeEach(function () {
    $this->course = Course::factory()->create(['slug' => 'correct-slug']);
    $this->route = route('courses.show', ['course' => $this->course->id, 'slug' => $this->course->slug]);
});

it('passes through when slug matches model slug', function () {
    $this->get($this->route)
        ->assertOk();
});

it('redirects when slug does not match model slug', function () {
    $this->get(route('courses.show', ['course' => $this->course->id, 'slug' => 'bad-slug']))
        ->assertRedirect($this->route);
});
