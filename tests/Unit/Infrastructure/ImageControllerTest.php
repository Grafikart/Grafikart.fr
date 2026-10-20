<?php

use App\Http\Front\ImageController;
use App\Infrastructure\Image\ImageUrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->imageDirectory = sys_get_temp_dir().'/glide-test-'.bin2hex(random_bytes(8));
    File::makeDirectory($this->imageDirectory.'/source', recursive: true);
    $this->app->usePublicPath($this->imageDirectory.'/source');
    config(['image.cache_path' => $this->imageDirectory.'/cache', 'image.driver' => 'gd', 'image.resize_key' => 'test-key']);

    $image = imagecreatetruecolor(100, 50);
    imagefilledrectangle($image, 0, 0, 99, 49, imagecolorallocate($image, 255, 0, 0));
    imagepng($image, public_path('sample.png'));
});

afterEach(function () {
    File::deleteDirectory($this->imageDirectory);
});

it('crops to WebP and serves cached images with HTTP cache headers', function () {
    $url = ImageUrlGenerator::resize('/sample.png', 20, 20);
    $controller = new ImageController;
    $response = $controller->resize(20, 20, 'sample.png', Request::create($url));

    expect($response->headers->get('Content-Type'))->toBe('image/webp')
        ->and($response->getMaxAge())->toBe(31_536_000)
        ->and($response->headers->get('Cache-Control'))->toContain('public');

    ob_start();
    $response->sendContent();
    $content = ob_get_clean();
    $dimensions = getimagesizefromstring($content);

    expect($dimensions[0])->toBe(20)
        ->and($dimensions[1])->toBe(20)
        ->and($dimensions['mime'])->toBe('image/webp')
        ->and((int) $response->headers->get('Content-Length'))->toBe(strlen($content));

    $cachedFiles = File::allFiles(config('image.cache_path'));
    expect($cachedFiles)->toHaveCount(1);

    $cached = $controller->resize(20, 20, 'sample.png', Request::create($url));
    ob_start();
    $cached->sendContent();
    expect(ob_get_clean())->toBe($content);

    $conditional = Request::create($url, server: ['HTTP_IF_MODIFIED_SINCE' => $response->headers->get('Last-Modified')]);
    expect($controller->resize(20, 20, 'sample.png', $conditional)->getStatusCode())->toBe(304);
});

it('rejects an invalid image signature', function () {
    try {
        (new ImageController)->resize(20, 20, 'sample.png', Request::create('/media/resize/20/20/sample.png?s=invalid'));
        test()->fail('Expected an invalid signature to be rejected.');
    } catch (HttpException $exception) {
        expect($exception->getStatusCode())->toBe(403);
    }
});
