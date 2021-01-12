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
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CommentApiProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{
    private RequestStack $requestStack;
    private CommentRepository $commentRepository;

    private UploaderHelper $uploaderHelper;

    public function __construct(
        RequestStack $requestStack,
        CommentRepository $commentRepository,
        UploaderHelper $uploaderHelper
    ) {
        $this->requestStack = $requestStack;
        $this->commentRepository = $commentRepository;
        $this->uploaderHelper = $uploaderHelper;
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
        if (null === $request) {
            throw new RuntimeException('Requête introuvable');
        }
        $contentId = (int) $request->get('content');
        if (0 === $contentId) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Aucun contenu ne correspond à cet ID');
        }

        return array_map(
            fn (Comment $comment) => CommentResource::fromComment($comment, $this->uploaderHelper),
            $this->commentRepository->findForApi($contentId)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param int|array $id
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?CommentResource
    {
        if (is_array($id)) {
            throw new RuntimeException('id as array not expected');
        }

        $comment = $this->commentRepository->findPartial((int) $id);

        return $comment ? CommentResource::fromComment($comment, $this->uploaderHelper) : null;
    }
}
