<?php

namespace App\Http\Controller;

use App\Domain\Application\Entity\Content;
use App\Domain\History\Event\ProgressEvent;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProgressionController extends AbstractController
{
    /**
     * @Route("/progress/{content}/{progress}", name="progress", methods={"POST"}, requirements={"progress"= "^([1-9][0-9]?|100)$"})
     * @IsGranted(App\Http\Security\ContentVoter::PROGRESS, subject="content")
     */
    public function progress(
        Content $content,
        int $progress,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();
        $dispatcher->dispatch(new ProgressEvent($content, $user, $progress));
        $em->flush();

        return new JsonResponse([]);
    }
}
