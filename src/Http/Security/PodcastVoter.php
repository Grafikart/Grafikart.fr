<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\PodcastService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PodcastVoter extends Voter
{
    final public const VOTE = 'VOTE_PODCAST';
    final public const CREATE = 'CREATE_PODCAST';
    final public const DELETE = 'DELETE_PODCAST';

    public function __construct(private readonly PodcastService $podcastService)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return
            in_array($attribute, [self::CREATE])
            || (in_array($attribute, [self::VOTE, self::DELETE]) && $subject instanceof Podcast);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!($user instanceof User)) {
            return false;
        }

        return match ($attribute) {
            self::VOTE => $subject instanceof Podcast && $this->canVote($user, $subject),
            self::DELETE => $subject instanceof Podcast && $this->canDelete($user, $subject),
            self::CREATE => $this->podcastService->canCreate($user),
            default => false,
        };
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
