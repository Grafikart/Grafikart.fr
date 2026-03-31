<?php

use App\Domains\Course\Formation;
use App\Http\Front\Data\FormationViewData;

it('generates the URL for a formation record', function () {
    $formation = Formation::factory()->create();

    $data = FormationViewData::from($formation);

    expect($data->url)->toBe(route('formations.show', $formation, false));
});

it('replaces an existing payload URL with the generated one', function () {
    $formation = Formation::factory()->create();
    $formation->url = '/wrong-url';

    $data = FormationViewData::from($formation);

    expect($data->url)->toBe(route('formations.show', $formation, false));
    expect($data->url)->not->toBe('/wrong-url');
});
