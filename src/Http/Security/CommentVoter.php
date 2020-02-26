<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    const DELETE = 'delete';
    const UPDATE = 'update';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [
            self::DELETE,
            self::UPDATE,
        ]) && $subject instanceof Comment;
    }

    /**
     * @param string $attribute
     * @param Comment $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }
        return $subject->getAuthor() !== null && $subject->getAuthor()->getId() === $user->getId();
    }

}
