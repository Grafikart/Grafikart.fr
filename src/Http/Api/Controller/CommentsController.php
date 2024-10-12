<?php

namespace App\Http\Api\Controller;

use App\Domain\Comment\CommentRepository;
use App\Domain\Comment\CommentService;
use App\Domain\Comment\DTO\CreateCommentDTO;
use App\Domain\Comment\DTO\UpdateCommentDTO;
use App\Domain\Comment\Entity\Comment;
use App\Http\Api\Resource\CommentResource;
use App\Http\Controller\AbstractController;
use App\Http\Security\CommentVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentsController extends AbstractController
{
    #[Route('/comments', name: 'comments', methods: ['GET'])]
    public function index(
        #[MapQueryParameter(name: 'content')]
        int $contentId,
        CommentRepository $commentRepository,
    ): JsonResponse {
        $comments = $commentRepository->findForApi($contentId);

        return $this->json(array_map(fn (Comment $comment) => CommentResource::fromComment($comment), $comments), context: ['groups' => ['read']]);
    }

    #[Route('/comments', methods: ['POST'])]
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CommentService $service,
    ): JsonResponse {
        $user = $this->getUser();
        $groups = $user === null ? ['anonymous', 'write'] : ['write'];
        $data = $serializer->deserialize(
            $request->getContent(),
            CreateCommentDTO::class,
            'json',
            ['groups' => $groups]
        );
        if (!($data instanceof CreateCommentDTO)) {
            throw new \RuntimeException('Expected CreateCommentDTO got '.$data::class);
        }

        $errors = $validator->validate($data, groups: $groups);
        if ($errors->count() > 0) {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $comment = $service->create($data);

        return $this->json(CommentResource::fromComment($comment), context: ['groups' => ['read']]);
    }

    #[Route('/comments/{comment}', name: 'comment', methods: ['DELETE'])]
    #[IsGranted(CommentVoter::DELETE, 'comment')]
    public function delete(
        Comment $comment,
        CommentService $service,
    ): JsonResponse {
        if ($comment->getId()) {
            $service->delete($comment->getId());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/comments/{comment}', methods: ['PUT'])]
    #[IsGranted(CommentVoter::UPDATE, 'comment')]
    public function update(
        #[MapRequestPayload(serializationContext: ['groups' => ['write']], validationGroups: ['write'])]
        UpdateCommentDTO $data,
        Comment $comment,
        CommentService $service,
    ): JsonResponse {
        $service->update($comment, $data);

        return $this->json(CommentResource::fromComment($comment), context: ['groups' => ['read']]);
    }
}
