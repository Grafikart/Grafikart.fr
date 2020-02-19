<?php

namespace App\Http\Admin\Controller;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\AttachmentUrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttachmentController extends BaseController
{

    private ValidatorInterface $validator;
    private AttachmentUrlGenerator $urlGenerator;

    public function __construct(ValidatorInterface $validator, AttachmentUrlGenerator $urlGenerator)
    {
        $this->validator = $validator;
        $this->urlGenerator = $urlGenerator;
    }

    public function validateRequest(Request $request): array
    {
        $errors = $this->validator->validate($request->files->get('file'), [
            new Image()
        ]);
        if ($errors->count() === 0) {
            return [true, null];
        }
        return [false, new JsonResponse(['error' => $errors->get(0)->getMessage()], 422)];
    }

    /**
     * @Route("/attachment/{attachment}", name="attachment_show", methods={"POST"})
     */
    public function update(Attachment $attachment, Request $request, EntityManagerInterface $em): JsonResponse
    {
        [$valid, $response] = $this->validateRequest($request);
        if (!$valid) {
            return $response;
        }
        $attachment->setFile($request->files->get('file'));
        $attachment->setCreatedAt(new \DateTime());
        $em->flush();
        return new JsonResponse([
            'id' => $attachment->getId(),
            'url' => $this->urlGenerator->generate($attachment)
        ]);
    }

}
