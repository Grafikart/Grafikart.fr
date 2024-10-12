<?php

namespace App\Http\Api\Controller\Forum;

use App\Domain\Forum\Entity\Report;
use App\Http\Controller\AbstractController;
use App\Http\Security\ForumVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/forum', name: 'forum_')]
class ForumReportController extends AbstractController
{
    #[Route(path: '/reports/{report}', name: 'report')]
    #[IsGranted(ForumVoter::DELETE_REPORT, subject: 'report')]
    public function delete(Report $report, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($report);
        $em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route(path: '/reports', name: 'reports')]
    #[IsGranted(ForumVoter::CREATE_REPORT)]
    public function create(
        #[MapRequestPayload(serializationContext: ['groups' => ['create:report']])]
        Report $report,
        EntityManagerInterface $em,
    ): JsonResponse {
        $report
            ->setAuthor($this->getUserOrThrow())
            ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($report);
        $em->flush();

        return $this->json($report, context: ['groups' => ['read:report']]);
    }
}
