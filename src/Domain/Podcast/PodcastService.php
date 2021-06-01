<?php

namespace App\Domain\Podcast;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\Entity\PodcastVote;
use App\Domain\Podcast\Repository\PodcastVoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class PodcastService
{

    private PodcastVoteRepository $podcastVoteRepository;
    private EntityManagerInterface $em;

    public function __construct(PodcastVoteRepository $podcastVoteRepository, EntityManagerInterface $em)
    {
        $this->podcastVoteRepository = $podcastVoteRepository;
        $this->em = $em;
    }

    public function toggleVote(Podcast $podcast, User $user): PodcastVote
    {
        $vote = $this->podcastVoteRepository->findOneBy(['podcast' => $podcast, 'voter' => $user]);
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
}
