<?php

namespace App\Infrastructure\Captcha;

use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class CaptchaImageService
{

    private string $holeImage;
    private string $backgroundImage;
    private string $noiseImage;

    public function __construct(
        string $imagePath,
        private readonly CaptchaKeyService $keyService
    ) {
        $number = random_int(1, 10);
        $this->backgroundImage = sprintf("%s/background%d.png", $imagePath, $number);
        $this->holeImage = $imagePath . '/hole.png';
        $this->noiseImage = $imagePath . '/noise.png';
    }

    public function generateImage(): Response
    {
        [$x, $y] = $this->keyService->getKey();
        $transparent = 'rgba(0,0,0,0)';
        $manager = new ImageManager(['driver' => 'imagick']);
        $img = $manager->make($this->backgroundImage);
        $imageWidth = $img->width();
        $img->insert(
            $this->noiseImage,
            'top-left',
            random_int($imageWidth * -1, 0),
            random_int($img->height() * -1, 0)
        );

        $hole = $manager->make($this->holeImage);

        // Puzzle piece
        $piece = $manager->make($this->holeImage);
        $piece->insert($img, 'top-left', -$x, -$y);
        $piece->mask($hole, true);

        // Add a hole in the image
        $hole->opacity(85);
        $img->insert($hole, 'top-left', $x, $y);
        $img->resizeCanvas($imageWidth + $piece->width(), $img->height(), 'left', false, $transparent);
        $img->insert($piece, 'top-left', $imageWidth, 0);

        return $img->response('png');
    }

}
