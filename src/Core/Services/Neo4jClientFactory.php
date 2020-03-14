<?php

namespace App\Core\Services;

use Everyman\Neo4j\Client;

class Neo4jClientFactory
{
    public static function createClient (string $host, string $username, string $password): Client
    {
        $client = new Client($host, 7474);
        $client->getTransport()->setAuth($username, $password);
        return $client;
    }
}
