<?php

return [
    'enabled' => env('DKIM_ENABLED', false),
    'private_key' => env('DKIM_PRIVATE_KEY', storage_path('dkim/dkim.pem')),
    'passphrase' => env('DKIM_PASSPHRASE', ''),
    'selector' => env('DKIM_SELECTOR', 'default'),
    'domain' => env('DKIM_DOMAIN', 'grafikart.fr'),
    'mailers' => ['smtp', 'sendmail', 'log', 'mail'],
];
