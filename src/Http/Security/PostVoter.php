<?php

namespace App\Http\Security;

use App\Domain\Blog\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    final public const SHOW = 'show';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
                self::SHOW,
            ]) && ($subject instanceof Post);
    }

    /**
     * @param string $attribute
     * @param Post   $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return $subject instanceof Post && $subject->getCreatedAt() < new \DateTime('-2 hours');
    }
}
