<?php

use App\Domains\Forum\TopicTag;

test('it derives slug from name', function () {
    $tag = new TopicTag;
    $tag->name = 'Some Tag Name';

    expect($tag->slug)->toBe('some-tag-name');
});
