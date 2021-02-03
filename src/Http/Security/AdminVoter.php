<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{

    private string $appEnv;

    public function __construct(string $appEnv)
    {
        $this->appEnv = $appEnv;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->appEnv === 'prod') {
            return 'Grafikart' === $user->getUsername() && $user->getId() === 1;
        }

        return 'Grafikart' === $user->getUsername();
    }
}
