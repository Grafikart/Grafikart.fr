<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\PodcastService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PodcastVoter extends Voter
{
    public const VOTE = 'VOTE_PODCAST';
    public const CREATE = 'CREATE_PODCAST';
    public const DELETE = 'DELETE_PODCAST';

    public function __construct(private PodcastService $podcastService)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return
            in_array($attribute, [self::CREATE]) ||
            (in_array($attribute, [self::VOTE, self::DELETE]) && $subject instanceof Podcast);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!($user instanceof User)) {
            return false;
        }

        switch ($attribute) {
            case self::VOTE:
                return $subject instanceof Podcast && $this->canVote($user, $subject);
            case self::DELETE:
                return $subject instanceof Podcast && $this->canDelete($user, $subject);
            case self::CREATE:
                return $this->podcastService->canCreate($user);
        }

        return false;
    }

    private function canVote(User $user, Podcast $podcast): bool
    {
        return $podcast->getAuthor()->getId() !== $user->getId() && null === $podcast->getScheduledAt();
    }

    private function canDelete(User $user, Podcast $podcast): bool
    {
        return $podcast->getAuthor()->getId() === $user->getId() && 1 === $podcast->getVotesCount();
    }
}
