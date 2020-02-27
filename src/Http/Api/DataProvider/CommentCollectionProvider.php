<?php

namespace App\Http\Api\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\RuntimeException;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentCollectionProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private RequestStack $requestStack;
    private CommentRepository $commentRepository;

    public function __construct(
        RequestStack $requestStack,
        CommentRepository $commentRepository
    )
    {
        $this->requestStack = $requestStack;
        $this->commentRepository = $commentRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Comment::class === $resourceClass;
    }

    /**
     * @return array<Comment>
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
        return $this->commentRepository->findForApi($contentId);
    }
}
