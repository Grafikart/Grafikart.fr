<?php

namespace App\Http\Controller;

use App\Http\DTO\CaptchaGuessDTO;
use App\Infrastructure\Captcha\CaptchaImageService;
use App\Infrastructure\Captcha\CaptchaKeyService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class CaptchaController extends AbstractController
{

    #[Route('/captcha', methods: ['GET'])]
    public function captcha(CaptchaImageService $captchaImageService): Response
    {
        return $captchaImageService->generateImage();
    }

    #[Route('/captcha/validate', methods: ['POST'])]
    public function validate(
        #[MapRequestPayload] CaptchaGuessDTO $guess,
        CaptchaKeyService $keyService,
    ): Response {
        $isValid = $keyService->verifyKey($guess->response);
        if ($isValid) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return new Response("{}", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
