<?php

namespace App\Http\Api\Controller;

use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractApiController extends AbstractController
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @param array<string> $groups
     */
    public function validateOrThrow(mixed $data, array $groups = []): void
    {
        $violations = $this->validator->validate($data, groups: $groups);
        if ($violations->count() > 0) {
            throw HttpException::fromStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY, implode("\n", array_map(static fn ($e) => $e->getMessage(), iterator_to_array($violations))), new ValidationFailedException($data, $violations));
        }
    }
}
