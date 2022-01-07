<?php

namespace App\Domain\Podcast;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\Entity\PodcastVote;
use App\Domain\Podcast\Repository\PodcastRepository;
use Doctrine\ORM\EntityManagerInterface;

class PodcastService
{
    public const LIMIT_PER_MONTH = 2;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function toggleVote(Podcast $podcast, User $user): PodcastVote
    {
        $podcastVoteRepository = $this->em->getRepository(PodcastVote::class);
        $vote = $podcastVoteRepository->findOneBy(['podcast' => $podcast, 'voter' => $user]);
        if ($vote) {
            $podcast->setVotesCount($podcast->getVotesCount() - 1);
            $this->em->remove($vote);
        } else {
            $vote = new PodcastVote($user, $podcast);
            $podcast->setVotesCount($podcast->getVotesCount() + 1);
            $this->em->persist($vote);
        }
        $this->em->flush();

        return $vote;
    }

    public function suggest(Podcast $podcast): Podcast
    {
        $this->em->persist($podcast);
        $this->em->flush();

        return $podcast;
    }

    public function canCreate(User $user): bool
    {
        /** @var PodcastRepository $podcastRepository */
        $podcastRepository = $this->em->getRepository(Podcast::class);

        return $user->getCreatedAt() < new \DateTime('-1 month') && $podcastRepository->countRecentFromUser($user) < self::LIMIT_PER_MONTH;
    }
}
