<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RevisionVoter extends Voter
{
    public final const ADD = 'add_revision';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::ADD,
        ]) && null === $subject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return true;
    }
}
