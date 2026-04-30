<?php

declare(strict_types=1);

return [
    'enabled' => env('DKIM_ENABLED', true),
    'private_key' => env('DKIM_PRIVATE_KEY', storage_path('dkim/dkim.pem')),
    'selector' => env('DKIM_SELECTOR', 'default'),
    'domain' => env('DKIM_DOMAIN', 'grafikart.fr'),
    'passphrase' => env('DKIM_PASSPHRASE', ''),
    'algorithm' => env('DKIM_ALGORITHM', 'rsa-sha256'),
    'identity' => env('DKIM_IDENTITY', null),
    'mailers' => env('DKIM_MAILERS', ['sendmail', 'mail']),
];
