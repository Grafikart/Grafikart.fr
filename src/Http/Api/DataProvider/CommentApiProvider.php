<?php

namespace App\Http\Api\DataProvider;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentRepository;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class CommentApiProvider implements ProviderInterface
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly UploaderHelper $uploaderHelper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Dans le cadre des collection, on impose un filtre par contenu
        if ($operation instanceof GetCollection) {
            if (!isset($context['filters']) || !isset($context['filters']["content"])) {
                throw new HttpException(Response::HTTP_BAD_REQUEST, 'Aucun contenu ne correspond à cet ID');
            }
            $contentId = (int) $context['filters']['content'];
             if (0 === $contentId) {
                 throw new HttpException(Response::HTTP_BAD_REQUEST, 'Aucun contenu ne correspond à cet ID');
             }

            return array_map(
                fn (Comment $comment) => CommentResource::fromComment($comment, $this->uploaderHelper),
                $this->commentRepository->findForApi($contentId)
            );
        }

        // Pour le reste des opérations on extrait les resources depuis la base de données
        if (!isset($uriVariables["id"])) {
            return null;
        }
        $id = $uriVariables["id"] ?: 0;
        $comment = $this->commentRepository->findPartial((int) $id);
        return $comment ? CommentResource::fromComment($comment, $this->uploaderHelper) : null;
    }
}
