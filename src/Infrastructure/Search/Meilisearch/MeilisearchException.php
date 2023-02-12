<?php

namespace App\Infrastructure\Search\Meilisearch;

use Symfony\Contracts\HttpClient\ResponseInterface;

final class MeilisearchException extends \RuntimeException
{
    public int $status;

    /**
     * @var string
     */
    public $message;

    public function __construct(ResponseInterface $response)
    {
        $this->status = $response->getStatusCode();
        $this->message = json_decode($response->getContent(false), true, 512, JSON_THROW_ON_ERROR)['message'] ?? '';
    }
}
