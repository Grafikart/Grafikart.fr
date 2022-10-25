<?php

namespace App\Infrastructure\Captcha;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Validates a CAPTCHA using the hCaptcha API.
 */
class IsValidCaptchaValidator extends ConstraintValidator
{
    /**
     * Constructs the validator from injected dependencies.
     */
    public function __construct(private readonly string $apiSecret, private readonly RequestStack $requestStack, private readonly HttpClientInterface $client)
    {
    }

    private function setAsInvalid(IsValidCaptcha $constraint): void
    {
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsValidCaptcha) {
            throw new UnexpectedTypeException($constraint, IsValidCaptchaValidator::class);
        }

        $value = $this->requestStack->getMainRequest()?->request->get('h-captcha-response');

        if (empty($value) || !is_string($value)) {
            $this->setAsInvalid($constraint);

            return;
        }

        try {
            $this->verifyCode($value);
        } catch (\Exception $e) {
            $constraint->message = $e->getMessage();
            $this->setAsInvalid($constraint);
        }
    }

    private function verifyCode(string $value): void
    {
        $response = $this->client->request(
            'POST',
            'https://hcaptcha.com/siteverify',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => http_build_query([
                    'secret' => $this->apiSecret,
                    'response' => $value,
                ]),
            ]
        );
        if ($response->getStatusCode() !== \Symfony\Component\HttpFoundation\Response::HTTP_OK) {
            throw new \Exception('Impossible de contacter le serveur hcaptcha');
        }

        $response = json_decode($response->getContent(), true);
        if (!($response['success'] ?? false)) {
            throw new \Exception('Captcha invalide');
        }

        return;
    }
}
