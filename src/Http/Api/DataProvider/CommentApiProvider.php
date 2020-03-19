<?php

namespace App\Http\Api\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\RuntimeException;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentRepository;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentApiProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{
    private RequestStack $requestStack;
    private CommentRepository $commentRepository;

    public function __construct(
        RequestStack $requestStack,
        CommentRepository $commentRepository
    ) {
        $this->requestStack = $requestStack;
        $this->commentRepository = $commentRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return CommentResource::class === $resourceClass;
    }

    /**
     * @return array<CommentResource>
     */
    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            throw new RuntimeException('Requête introuvable');
        }
        $contentId = (int)$request->get('content');
        if ($contentId === 0) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Aucun contenu ne correspond à cet ID');
        }
        return array_map(fn(Comment $comment) => CommentResource::fromComment($comment),
            $this->commentRepository->findForApi($contentId));
    }

    /**
     * @inheritDoc
     * @param int $id
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if (is_array($id)) {
            throw new RuntimeException('id as array not expected');
        }
        return CommentResource::fromComment($this->commentRepository->findPartial((int)$id));
    }
}
