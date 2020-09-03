<?php

namespace App\Http\Controller\Profile;

use App\Domain\History\Entity\Progress;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeleteProgressController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }

    /**
     * @Route("/progress/{id}", name="delete_progress", methods={"DELETE"})
     * @IsGranted("DELETE_PROGRESS", subject="progress")
     */
    public function deleteProgress(Progress $progress): JsonResponse
    {
        $this->em->remove($progress);
        $this->em->flush();
        return new JsonResponse([]);
    }
}
