<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\IndexerInterface;
use Symfony\Component\HttpFoundation\Response;

class TypesenseIndexer implements IndexerInterface
{

    private TypesenseClient $client;

    public function __construct(TypesenseClient $client)
    {
        $this->client = $client;
    }

    public function index(array $data): void
    {
        try {
            $this->client->get("collections/content/documents/{$data['id']}");
            $this->client->delete("collections/content/documents/{$data['id']}");
            $this->index($data);
        } catch (TypesenseException $exception) {
            if ($exception->status === Response::HTTP_NOT_FOUND && $exception->message === 'Not Found') {
                $this->client->post("collections", [
                    "name" => "content",
                    "fields" => [
                        ["name" => "title", "type" => "string"],
                        ["name" => "content", "type" => "string"],
                        ["name" => "category", "type" => "string[]"],
                        ["name" => "type", "type" => "string", "facet" => true],
                        ["name" => "created_at", "type" => "int32"],
                    ],
                    'default_sorting_field' => 'created_at'
                ]);
                $this->index($data);
            } elseif ($exception->status === Response::HTTP_NOT_FOUND) {
                $this->client->post("collections/content/documents", $data);
            }
        }
    }
}
