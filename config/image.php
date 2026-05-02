<?php

return [
    'resize_key' => env('IMAGE_RESIZE_KEY', 'change-me-in-production'),
    'cache_path' => storage_path('framework/cache/images'),
    'driver' => env('IMAGE_DRIVER', 'gd'),
];
