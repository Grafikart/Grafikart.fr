<?php

namespace App\Infrastructure\Captcha;

use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $manager = new ImageManager(new Driver());
        $img = $manager->read($this->backgroundImage);
        $imageWidth = $img->width();

        // Ajoute du bruit à l'image
        $img->place(
            $this->noiseImage,
            'top-left',
            random_int($imageWidth * -1, 0),
            random_int($img->height() * -1, 0)
        );

        $hole = $manager->read($this->holeImage);

        // Pièce du puzzle
        $piece = $manager->read($this->holeImage);
        $piece->place($img, 'top-left', -$x, -$y);
        $piece = $this->applyMaskImagick($manager, $piece, $hole);

        // On ajoute l'overlay de la pièce au puzzle
        $img->place($hole, 'top-left', $x, $y, 85);
        $img->pad($imageWidth + $piece->width(), $img->height(), $transparent, 'top-left');
        // On ajoute la pièce sur le côté (à droite de l'image)
        $img->place($piece, 'top-left', $imageWidth, 0);

        // On envoie la réponse au client
        $stream = $img->toPng(indexed: true)->toFilePointer();

        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Transfer-Encoding', 'binary',
            'Content-Type' => 'image/png',
            'Content-Length' => fstat($stream)['size'] ?? 0,
        ]);
    }

    private function applyMaskImagick(ImageManager $manager, ImageInterface $img, ImageInterface $mask): ImageInterface
    {
        $imagick = $img->core()->native();
        $maskImagick = $mask->core()->native();

        $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_SET);

        $imagick->compositeImage(
            $maskImagick,
            \Imagick::COMPOSITE_DSTIN,
            0,
            0
        );

        return $manager->read($imagick);
    }
}
