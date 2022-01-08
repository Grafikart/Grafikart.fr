<?php

namespace App\Infrastructure\Storage;

use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxFilesystemFactory
{
    public function __construct(private readonly string $accessToken)
    {
    }

    public function createFilesystem(): Filesystem
    {
        $client = new Client($this->accessToken);
        $adapter = new DropboxAdapter($client);

        return new Filesystem($adapter, ['case_sensitive' => false]);
    }
}
