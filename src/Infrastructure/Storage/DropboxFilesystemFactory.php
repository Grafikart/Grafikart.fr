<?php

namespace App\Infrastructure\Storage;

use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxFilesystemFactory
{
    private string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function createFilesystem(): Filesystem
    {
        $client = new Client($this->accessToken);
        $adapter = new DropboxAdapter($client);

        return new Filesystem($adapter, ['case_sensitive' => false]);
    }
}
