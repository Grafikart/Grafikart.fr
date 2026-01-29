<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the search engines below you wish to use
    | as your default engine for search operations. This is the engine which
    | will be utilized unless another engine is explicitly specified when
    | you execute a search query.
    |
    */

    'default' => env('SEARCH_ENGINE', 'typesense'),

    /*
    |--------------------------------------------------------------------------
    | Search Engine Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the search engine connections defined for your
    | application. An example configuration is provided for each engine
    | which is supported. You're free to add / remove connections.
    |
    */

    'engines' => [

        'typesense' => [
            'driver' => 'typesense',
            'endpoint' => env('TYPESENSE_URL', 'http://localhost:8108'),
            'key' => env('TYPESENSE_KEY', ''),
        ],

        'meilisearch' => [
            'driver' => 'meilisearch',
            'endpoint' => env('MEILISEARCH_URL', 'http://localhost:7700'),
            'key' => env('MEILISEARCH_KEY', ''),
        ],

    ],

];
