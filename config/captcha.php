<?php

return [
    'driver' => env('CAPTCHA_DRIVER', ''),
    'turnstile' => [
        'id' => env('TURNSTILE_ID'),
        'secret' => env('TURNSTILE_SECRET'),
    ],
];
