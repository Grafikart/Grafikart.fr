<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\History\Entity\Progress;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProgressVoter extends Voter
{
    final public const DELETE_PROGRESS = 'DELETE_PROGRESS';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
            self::DELETE_PROGRESS,
        ]) && $subject instanceof Progress;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user instanceof User
            && $subject instanceof Progress
            && $subject->getAuthor()->getId() === $user->getId();
    }
}
