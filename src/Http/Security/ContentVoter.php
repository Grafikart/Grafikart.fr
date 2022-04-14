<?php

namespace App\Http\Security;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ContentVoter extends Voter
{
    public final const PROGRESS = 'progress';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                self::PROGRESS,
            ]) && $subject instanceof Content;
    }

    /**
     * @param Content $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $contentIsPublished = !$subject->isScheduled() && $subject->isOnline();

        return $user instanceof User && ($user->isPremium() || $contentIsPublished);
    }
}
