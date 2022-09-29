<?php

namespace App\Http\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChannelVoter extends Voter
{
    final public const LISTEN_ADMIN = 'channel_listen_admin';

    protected function supports(string $attribute, $subject): bool
    {
        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return false;
    }
}
