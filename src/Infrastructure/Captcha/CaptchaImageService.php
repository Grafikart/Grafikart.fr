<?php

namespace App\Infrastructure\Captcha;

use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Génère des captcha puzzle.
 */
class CaptchaImageService
{
    private readonly string $holeImage;
    private readonly string $backgroundImage;
    private readonly string $noiseImage;

    public function __construct(
        string $imagePath,
        private readonly CaptchaKeyService $keyService,
    ) {
        $number = random_int(1, 10);
        $this->backgroundImage = sprintf('%s/background%d.png', $imagePath, $number);
        $this->holeImage = $imagePath.'/hole.png';
        $this->noiseImage = $imagePath.'/noise.png';
    }

    public function generateImage(): Response
    {
        [$x, $y] = $this->keyService->getKey();
        $transparent = 'rgba(0,0,0,0)';
        $manager = new ImageManager(['driver' => 'imagick']);
        $img = $manager->make($this->backgroundImage);
        $imageWidth = $img->width();

        // Ajoute du bruit à l'image
        $img->insert(
            $this->noiseImage,
            'top-left',
            random_int($imageWidth * -1, 0),
            random_int($img->height() * -1, 0)
        );

        $hole = $manager->make($this->holeImage);

        // Pièce du puzzle
        $piece = $manager->make($this->holeImage);
        $piece->insert($img, 'top-left', -$x, -$y);
        $piece->mask($hole, true);

        // On ajoute l'overlay de la pièce au puzzle
        $hole->opacity(85);
        $img->insert($hole, 'top-left', $x, $y);
        $img->resizeCanvas($imageWidth + $piece->width(), $img->height(), 'left', false, $transparent);
        // On ajoute la pièce sur le côté (à droite de l'image)
        $img->insert($piece, 'top-left', $imageWidth, 0);

        return $img->response('png');
    }
}
