<?php

namespace App\Infrastructure\Search\Meilisearch;

use Illuminate\Http\Client\Response;

final class MeilisearchException extends \RuntimeException
{
    public int $status;

    /** @var string */
    public $message;

    public function __construct(Response $response)
    {
        $this->status = $response->status();
        $this->message = $response->json('message') ?? '';
    }
}
