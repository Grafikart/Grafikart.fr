<?php

namespace App\Http\Api\Controller\Forum;

use App\Domain\Forum\Entity\Report;
use App\Http\Controller\AbstractController;
use App\Http\Security\ForumVoter;
use App\Normalizer\EntityDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/forum', name: 'forum_')]
class ForumReportController extends AbstractController
{
    #[Route(path: '/reports/{report}', name: 'report', methods: ['DELETE'])]
    #[IsGranted(ForumVoter::DELETE_REPORT, subject: 'report')]
    public function delete(Report $report, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($report);
        $em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route(path: '/reports', name: 'reports', methods: ['POST'])]
    #[IsGranted(ForumVoter::CREATE_REPORT)]
    public function create(
        #[MapRequestPayload(serializationContext: ['groups' => ['create:report'], EntityDenormalizer::HYDRATE_RELATIONS => true])]
        Report $report,
        EntityManagerInterface $em,
    ): JsonResponse {
        $report
            ->setAuthor($this->getUserOrThrow())
            ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($report);
        $em->flush();

        return $this->json($report, status: Response::HTTP_CREATED, context: ['groups' => ['read:report']]);
    }
}
