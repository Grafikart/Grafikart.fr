<?php declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\PodcastService;
use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PodcastController extends AbstractController
{
    /**
     * @Route("/podcasts/{id<\d+>}/vote", name="podcast_vote")
     * @IsGranted("VOTE_PODCAST", subject="podcast")
     */
    public function vote(Podcast $podcast, PodcastService $podcastService): JsonResponse
    {
        $podcastService->toggleVote($podcast, $this->getUserOrThrow());
        return new JsonResponse([
            'votesCount' => $podcast->getVotesCount()
        ]);
    }
}
