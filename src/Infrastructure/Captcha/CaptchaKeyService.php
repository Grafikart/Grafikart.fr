<?php

namespace App\Infrastructure\Captcha;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CaptchaKeyService
{

    public const CAPTCHA_WIDTH = 350;
    public const CAPTCHA_HEIGHT= 200;
    public const CAPTCHA_PIECE_WIDTH = 80;
    public const CAPTCHA_PIECE_HEIGHT = 50;
    private const MARGIN_OF_ERROR = 5;
    private const SESSION_KEY = 'CAPTCHA';
    private const SESSION_KEY_TRIES = 'CAPTCHA_TRIES';
    private const MAX_TRY = 3;

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function generateKey(): void
    {
        $x = rand(0, self::CAPTCHA_WIDTH - self::CAPTCHA_PIECE_WIDTH);
        $y = rand(0, self::CAPTCHA_HEIGHT - self::CAPTCHA_PIECE_HEIGHT);
        $session = $this->getSession();
        $session->set(self::SESSION_KEY, [$x, $y]);
        $session->set(self::SESSION_KEY_TRIES, 0);
        $session->save();
    }

    /**
     * @return int[]
     */
    public function getKey(): array
    {
        return $this->getSession()->get(self::SESSION_KEY);
    }

    public function verifyKey(string $guessKey): bool
    {
        $guess = array_map(fn(string $v) => intval($v), explode('-', $guessKey));
        $key = $this->getSession()->get(self::SESSION_KEY);
        if ($key === null) {
            return false;
        }
        for($i = 0; $i < count($key); $i++) {
            // Le nombre est trop petit ou trop grand
            $min = $key[$i] - self::MARGIN_OF_ERROR;
            $max = $key[$i] + self::MARGIN_OF_ERROR;
            if ($guess[$i] < $min || $guess[$i] > $max) {
                $session = $this->getSession();
                $tries = ($session->get(self::SESSION_KEY_TRIES) ?? 0) + 1;
                if ($tries >= self::MAX_TRY) {
                    $this->generateKey();
                    throw new TooManyTryException();
                }
                $session->set(self::SESSION_KEY_TRIES, $tries);
                $session->save();
                return false;
            }
        }
        return true;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

}
