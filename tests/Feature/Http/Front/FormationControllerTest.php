<?php

use App\Domains\Course\Formation;

describe('show', function () {
    it('redirects to replacement formation when force_redirect is true', function () {
        $replacement = Formation::factory()->online()->create();
        $deprecated = Formation::factory()->create([
            'deprecated_by_id' => $replacement->id,
            'force_redirect' => true,
        ]);

        $this->get("/formations/{$deprecated->slug}")
            ->assertRedirect(route('formations.show', ['formation' => $replacement->slug]))
            ->assertStatus(301);
    });

    it('does not redirect when force_redirect is false', function () {
        $replacement = Formation::factory()->online()->create();
        $deprecated = Formation::factory()->online()->withChapters(1, 1)->create([
            'deprecated_by_id' => $replacement->id,
            'force_redirect' => false,
        ]);

        $this->get("/formations/{$deprecated->slug}")
            ->assertSuccessful();
    });

    it('does not redirect when no replacement formation is set', function () {
        $formation = Formation::factory()->online()->withChapters(1, 1)->create(['force_redirect' => true]);

        $this->get("/formations/{$formation->slug}")
            ->assertSuccessful();
    });
});
