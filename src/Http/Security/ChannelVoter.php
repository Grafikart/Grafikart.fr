<?php

namespace App\Http\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChannelVoter extends Voter
{
    public final const LISTEN_ADMIN = 'channel_listen_admin';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return false;
    }
}
