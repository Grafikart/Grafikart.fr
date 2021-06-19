<?php

namespace App\Domain\Podcast;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\Entity\PodcastVote;
use Doctrine\ORM\EntityManagerInterface;

class PodcastService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        return false;
    }
}
