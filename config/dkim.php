<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable DKIM Signing
    |--------------------------------------------------------------------------
    */
    'enabled' => env('DKIM_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | DKIM Private Key
    |--------------------------------------------------------------------------
    | Path to the private key file, or the raw key string.
    | Example: storage_path('dkim/private.key')
    */
    'private_key' => env('DKIM_PRIVATE_KEY', storage_path('dkim/dkim.pem')),

    /*
    |--------------------------------------------------------------------------
    | DKIM Passphrase
    |--------------------------------------------------------------------------
    | Passphrase for the private key, if any.
    */
    'passphrase' => env('DKIM_PASSPHRASE', ''),

    /*
    |--------------------------------------------------------------------------
    | DKIM Selector
    |--------------------------------------------------------------------------
    | The DNS selector for the DKIM record (e.g. "default", "mail", "s1").
    */
    'selector' => env('DKIM_SELECTOR', 'default'),

    /*
    |--------------------------------------------------------------------------
    | DKIM Domain
    |--------------------------------------------------------------------------
    | The domain to sign for. Typically your sending domain.
    */
    'domain' => env('DKIM_DOMAIN', 'grafikart.fr'),

    /*
    |--------------------------------------------------------------------------
    | Headers to Sign
    |--------------------------------------------------------------------------
    | List of headers to include in the DKIM signature.
    */
    'signed_headers' => [
        'From',
        'To',
        'Subject',
        'Date',
        'MIME-Version',
        'Content-Type',
        'Reply-To',
        'Message-ID',
    ],

];
