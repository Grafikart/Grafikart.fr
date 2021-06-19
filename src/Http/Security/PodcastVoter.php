<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\PodcastService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PodcastVoter extends Voter
{
    const VOTE = 'VOTE_PODCAST';
    const CREATE = 'CREATE_PODCAST';
    private PodcastService $podcastService;

    public function __construct(PodcastService $podcastService)
    {
        $this->podcastService = $podcastService;
    }

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

        if (!($user instanceof User)) {
            return false;
        }

        switch ($attribute) {
            case self::VOTE:
                return $subject instanceof Podcast && $this->canVote($user, $subject);
            case self::CREATE:
                return $this->canCreate($user);
        }

        return false;
    }

    private function canVote(User $user, Podcast $podcast): bool
    {
        return $podcast->getAuthor()->getId() !== $user->getId() && null === $podcast->getConfirmedAt();
    }

    private function canCreate(User $user): bool
    {
        return $this->podcastService->canCreate($user);
    }
}
