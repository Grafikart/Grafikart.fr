<?php

namespace App\Http\Api\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Domain\Contact\ContactData;
use App\Domain\Contact\ContactService;
use App\Domain\Contact\TooManyContactException;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ContactController extends AbstractController
{
    #[Route(path: '/contact', name: 'api_contact', methods: ['POST'])]
    public function create(
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator,
        ContactService $contactService,
        Request $request,
    ): JsonResponse {
        $data = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        /** @var ContactData $contactData */
        $contactData = $denormalizer->denormalize($data, ContactData::class);
        $validator->validate($contactData);
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
