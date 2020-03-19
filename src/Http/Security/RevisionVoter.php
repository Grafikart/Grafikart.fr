<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RevisionVoter extends Voter
{
    const ADD = 'add_revision';

    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
            self::ADD,
        ]) && $subject === null;
    }

    /**
     * @param string $attribute
     * @param Comment|CommentResource $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return true;
    }

}
