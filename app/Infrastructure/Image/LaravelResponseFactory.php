<?php

namespace App\Infrastructure\Image;

use Illuminate\Http\Request;
use League\Flysystem\FilesystemOperator;
use League\Glide\Responses\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaravelResponseFactory implements ResponseFactoryInterface
{
    public function __construct(protected ?Request $request = null) {}

    public function create(FilesystemOperator $cache, $path): StreamedResponse
    {
        $stream = $cache->readStream($path);

        $response = new StreamedResponse;
        $response->headers->set('Content-Type', $cache->mimeType($path));
        $response->headers->set('Content-Length', (string) $cache->fileSize($path));
        $response->setPublic();
        $response->setMaxAge(31_536_000);
        $response->setExpires(new \DateTimeImmutable('+ 1 years'));

        if ($this->request) {
            $response->setLastModified(new \DateTimeImmutable(sprintf('@%s', $cache->lastModified($path))));
            $response->isNotModified($this->request);
        }

        $response->setCallback(function () use ($stream) {
            if (ftell($stream) !== 0) {
                rewind($stream);
            }
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
