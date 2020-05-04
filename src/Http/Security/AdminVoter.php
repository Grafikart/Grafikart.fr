<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getUsername() === 'Grafiakart';
    }
}
