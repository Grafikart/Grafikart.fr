<?php

namespace App\Http\Api\Resource;

use App\Domain\Comment\CommentData;
use App\Domain\Comment\Entity\Comment;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CommentResource extends CommentData
{
    #[Groups(['read'])]
    public ?int $id = null;

    #[Groups(['read'])]
    public ?string $username = null;

    #[Groups(['read'])]
    public string $html = '';

    #[Groups(['read'])]
    public string $content = '';

    #[Groups(['read'])]
    public ?string $avatar = null;

    #[Groups(['read'])]
    public ?int $target = null;

    #[Groups(['read'])]
    public int $createdAt = 0;

    #[Groups(['read'])]
    public ?int $parent = 0;

    #[Groups(['read'])]
    public ?int $userId = null;

    public static function fromComment(Comment $comment, ?UploaderHelper $uploaderHelper = null): CommentResource
    {
        $resource = new self();
        $author = $comment->getAuthor();
        $resource->id = $comment->getId();
        $resource->username = $comment->getUsername();
        $resource->content = $comment->getContent();
        $resource->html = strip_tags(
            (string) (new \Parsedown())
                ->setBreaksEnabled(true)
                ->setSafeMode(true)
                ->text($comment->getContent()),
            '<p><pre><code><ul><ol><li>'
        );
        $resource->createdAt = $comment->getCreatedAt()->getTimestamp();
        $resource->parent = null !== $comment->getParent() ? $comment->getParent()->getId() : 0;
        if ($author && $uploaderHelper && $author->getAvatarName()) {
            $resource->avatar = $uploaderHelper->asset($author, 'avatarFile');
        } else {
            $resource->avatar = '/images/default.png';
        }
        $resource->userId = $author ? $author->getId() : null;

        return $resource;
    }
}
