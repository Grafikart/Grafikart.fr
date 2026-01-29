<?php

namespace App\Infrastructure\Search\Typesense;

use Illuminate\Http\Client\Response;

final class TypesenseException extends \RuntimeException
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
