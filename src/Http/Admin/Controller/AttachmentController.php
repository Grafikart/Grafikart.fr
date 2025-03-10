<?php

namespace App\Http\Admin\Controller;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\Repository\AttachmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ATTACHMENT')]
class AttachmentController extends BaseController
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validateRequest(Request $request): array
    {
        $errors = $this->validator->validate($request->files->get('file'), [
            new Image(),
        ]);
        if (0 === $errors->count()) {
            return [true, null];
        }

        return [false, new JsonResponse(['error' => $errors->get(0)->getMessage()], 422)];
    }

    #[Route(path: '/attachment/folders', name: 'attachment_folders')]
    public function folders(AttachmentRepository $repository): JsonResponse
    {
        return new JsonResponse($repository->findYearsMonths());
    }

    #[Route(path: '/attachment/files', name: 'attachment_files')]
    public function files(AttachmentRepository $repository, Request $request): JsonResponse
    {
        ['path' => $path, 'q' => $q] = $this->getFilterParams($request);
        if ($q === 'orphan') {
            $attachments = $repository->orphaned();
        } elseif (!empty($q)) {
            $attachments = $repository->search($q);
        } elseif (null === $path) {
            $attachments = $repository->findLatest();
        } else {
            $attachments = $repository->findForPath($request->get('path'));
        }

        return $this->json($attachments);
    }

    #[Route(path: '/attachment/{attachment<\d+>?}', name: 'attachment_show', methods: ['POST'])]
    public function update(?Attachment $attachment, Request $request, EntityManagerInterface $em): JsonResponse
    {
        [$valid, $response] = $this->validateRequest($request);
        if (!$valid) {
            return $response;
        }
        if (null === $attachment) {
            $attachment = new Attachment();
        }
        $attachment->setFile($request->files->get('file'));
        $attachment->setCreatedAt(new \DateTimeImmutable());
        $em->persist($attachment);
        $em->flush();

        return $this->json($attachment);
    }

    #[Route(path: '/attachment/{attachment<\d+>}', methods: ['DELETE'])]
    public function delete(Attachment $attachment, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($attachment);
        $em->flush();

        return $this->json([]);
    }

    private function getFilterParams(Request $request): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'path' => null,
            'q' => null,
        ]);
        $resolver->setAllowedTypes('path', ['string', 'null']);
        $resolver->setAllowedTypes('q', ['string', 'null']);
        $resolver->setAllowedValues('path', fn ($value) => null === $value || preg_match('/^2\d{3}\/(1[0-2]|0[1-9])$/', (string) $value) > 0);

        try {
            return $resolver->resolve($request->query->all());
        } catch (InvalidOptionsException $exception) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $exception->getMessage());
        }
    }
}
