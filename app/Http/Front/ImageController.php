<?php

namespace App\Http\Front;

use App\Http\Controller;
use App\Infrastructure\Image\LaravelResponseFactory;
use Illuminate\Http\Request;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Handle resized image requests
 */
class ImageController extends Controller
{
    public function resize(int $width, int $height, string $path, Request $request): StreamedResponse
    {
        $server = ServerFactory::create([
            'source' => public_path(),
            'cache' => config('image.cache_path'),
            'driver' => config('image.driver'),
            'response' => new LaravelResponseFactory($request),
            'defaults' => [
                'q' => 75,
                'fm' => 'webp',
                'fit' => 'crop',
            ],
        ]);

        try {
            SignatureFactory::create(config('image.resize_key'))->validateRequest($request->path(), ['s' => $request->get('s')]);

            return $server->getImageResponse($path, ['w' => $width, 'h' => $height, 'fit' => 'crop']);
        } catch (SignatureException) {
            throw new HttpException(403, 'Signature invalide');
        }
    }
}
