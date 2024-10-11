<?php

namespace App\Http\Api\Controller;

use App\Domain\Forum\Entity\Report;
use App\Http\Controller\AbstractController;
use App\Http\Security\ForumVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ForumReportController extends AbstractController
{

    #[Route(path: '/forum/reports/{report}', name: 'forum_report')]
    #[IsGranted(ForumVoter::DELETE_REPORT, subject: 'report')]
    public function delete(Report $report, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($report);
        $em->flush();
        return new JsonResponse(null, 204);
    }

    #[Route(path: '/forum/reports', name: 'forum_reports')]
    #[IsGranted(ForumVoter::CREATE_REPORT)]
    public function create(
        #[MapRequestPayload(serializationContext: ['groups' => ['create:report']])]
        Report $report,
        EntityManagerInterface $em,
    )
    {
        $report
            ->setAuthor($this->getUserOrThrow())
            ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($report);
        $em->flush();
        return $this->json($report, context: ['groups' => ['read:report']]);
    }

}
