<?php

namespace App\Http\Security;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ContentVoter extends Voter
{
    public final const PROGRESS = 'progress';

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
                self::PROGRESS,
            ]) && $subject instanceof Content;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user instanceof User;
    }
}
