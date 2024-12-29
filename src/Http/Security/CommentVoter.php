<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Comment\CommentRepository;
use App\Domain\Comment\Entity\Comment;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    final public const DELETE = 'delete';
    final public const UPDATE = 'update';
    final public const CREATE = 'CREATE_COMMENT';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly CommentRepository $commentRepository,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [
            self::DELETE,
            self::UPDATE,
        ]) && ($subject instanceof Comment || $subject instanceof CommentResource);
    }

    /**
     * @param string                  $attribute
     * @param Comment|CommentResource $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if ($attribute === self::CREATE) {
            return $this->canCreate($token->getUser());
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($subject instanceof CommentResource) {
            $subject = $subject->entity;
        }

        if (null === $subject) {
            return false;
        }

        return null !== $subject->getAuthor() && $subject->getAuthor()->getId() === $user->getId();
    }

    private function canCreate(?UserInterface $user): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $ip = $request->getClientIp();
        if (!$ip) {
            return false;
        }

        return $this->commentRepository->hasIpCommentedRecently(
            $ip,
            $user instanceof User ? '-1 minutes' : '-5 minutes'
        );
    }
}
