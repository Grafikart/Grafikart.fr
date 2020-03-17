<?php

namespace App\Http\Security;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ContentVoter extends Voter
{
    const PROGRESS = 'progress';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
                self::PROGRESS,
            ]) && $subject instanceof Content;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        return $user instanceof User;
    }
}
