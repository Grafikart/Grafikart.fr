<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PodcastVoter extends Voter
{
    const VOTE = 'VOTE';

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
            self::VOTE,
        ]) && $subject instanceof Podcast;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        return
            $user instanceof User &&
            $subject instanceof Podcast &&
            $subject->getAuthor()->getId() !== $user->getId() &&
            $subject->getConfirmedAt() === null;
    }
}
