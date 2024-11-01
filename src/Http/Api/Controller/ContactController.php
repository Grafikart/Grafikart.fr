<?php

namespace App\Http\Api\Controller;

use App\Domain\Contact\ContactData;
use App\Domain\Contact\ContactService;
use App\Domain\Contact\TooManyContactException;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route(path: '/contact', name: 'contact', methods: ['POST'])]
    public function create(
        #[MapRequestPayload]
        ContactData $contactData,
        ContactService $contactService,
        Request $request,
    ): JsonResponse {
        try {
            $contactService->send($contactData, $request);
        } catch (TooManyContactException) {
            return new JsonResponse([
                'title' => 'Vous avez fait trop de demandes de contact consécutives.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
