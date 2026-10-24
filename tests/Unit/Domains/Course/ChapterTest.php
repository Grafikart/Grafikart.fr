<?php

use App\Domains\Course\Chapter;

it('normalizes sparse course indexes into a list', function () {
    $chapter = new Chapter('Découverte', [3 => 1315, 4 => 1316, 6 => 1318]);

    expect($chapter->ids)->toBe([1315, 1316, 1318]);
});
