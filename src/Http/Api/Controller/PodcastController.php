<?php

declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\PodcastService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PodcastController extends AbstractController
{
    #[Route(path: '/podcasts/{id<\d+>}/vote', name: 'podcast_vote')]
    #[IsGranted('VOTE_PODCAST', subject: 'podcast')]
    public function vote(Podcast $podcast, PodcastService $podcastService): JsonResponse
    {
        $podcastService->toggleVote($podcast, $this->getUserOrThrow());

        return new JsonResponse([
            'votesCount' => $podcast->getVotesCount(),
        ]);
    }

    #[Route(path: '/podcasts/{id<\d+>}', name: 'podcast_delete', methods: ['DELETE'])]
    #[IsGranted('DELETE_PODCAST', subject: 'podcast')]
    public function delete(Podcast $podcast, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($podcast);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}
