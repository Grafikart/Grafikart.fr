<?php

test('debugbar collects session data by default', function () {
    expect(config('debugbar.collectors.session'))->toBeTrue();
});
