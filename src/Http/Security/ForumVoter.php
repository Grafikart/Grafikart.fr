<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ForumVoter extends Voter
{

    const CREATE = "forumCreate";

    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [self::CREATE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        return $user instanceof User;
    }
}
