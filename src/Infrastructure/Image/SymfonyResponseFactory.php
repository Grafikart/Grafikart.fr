<?php

namespace App\Infrastructure\Image;

use League\Flysystem\FilesystemOperator;
use League\Glide\Responses\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SymfonyResponseFactory implements ResponseFactoryInterface
{
    public function __construct(protected ?Request $request = null)
    {
    }

    /**
     * Create the response.
     *
     * @param FilesystemOperator $cache the cache file system
     * @param string             $path  the cached file path
     */
    public function create(FilesystemOperator $cache, $path): StreamedResponse
    {
        $stream = $cache->readStream($path);

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $cache->mimeType($path));
        $response->headers->set('Content-Length', (string) $cache->fileSize($path));
        $response->setPublic();
        $response->setMaxAge(31_536_000);
        $response->setExpires(new \DateTime('+ 1 years'));

        if ($this->request) {
            $response->setLastModified(new \DateTime(sprintf('@%s', $cache->lastModified($path))));
            $response->isNotModified($this->request);
        }

        $response->setCallback(function () use ($stream) {
            if (0 !== ftell($stream)) {
                rewind($stream);
            }
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
