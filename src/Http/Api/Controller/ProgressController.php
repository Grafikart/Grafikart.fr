<?php

namespace App\Http\Api\Controller;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\History\Entity\Progress;
use App\Domain\History\Event\ProgressEvent;
use App\Domain\History\Exception\AlreadyFinishedException;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class ProgressController extends AbstractController
{
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $em;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    /**
     * @Route("/progress/{content<\d+>}/{progress}", name="progress", methods={"POST"}, requirements={"progress"= "^([1-9][0-9]{0,2}|1000)$"})
     * @IsGranted(App\Http\Security\ContentVoter::PROGRESS, subject="content")
     */
    public function progress(
        Content $content,
        int $progress
    ): JsonResponse {
        $user = $this->getUser();
        try {
            $this->dispatcher->dispatch(new ProgressEvent($content, $user, $progress / Progress::TOTAL));
        } catch (AlreadyFinishedException $e) {
            return new JsonResponse([
                'title' => 'Vous avez déjà terminé ce cours',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->em->flush();

        if (Progress::TOTAL !== $progress) {
            return new JsonResponse([]);
        }

        $button = null;
        if ($content instanceof Course && $content->getFormation() instanceof Formation) {
            /** @var Formation $formation */
            $formation = $content->getFormation();
            $nextChapterId = $formation->getNextCourseId($content->getId());
            if ($nextChapterId) {
                $button = [
                    'title' => 'Voir le chapitre suivant',
                    'anchor' => 'autoplay',
                    'target' => $this->em->getRepository(Course::class)->find($nextChapterId),
                ];
            }
        } elseif ($content instanceof Course) {
            $technologies = $content->getMainTechnologies();
            if (count($technologies) > 0) {
                $button = [
                    'title' => "Voir d'autres vidéos {$technologies[0]->getName()}",
                    'anchor' => 'tutoriels',
                    'target' => $technologies[0],
                ];
            }
        }

        return new JsonResponse([
            'message' => $this->renderView('courses/success.html.twig', [
                'button' => $button,
            ]),
        ]);
    }

    /**
     * @Route("/progress/{id<\d+>}", name="delete_progress", methods={"DELETE"})
     * @IsGranted("DELETE_PROGRESS", subject="progress")
     */
    public function deleteProgress(Progress $progress): JsonResponse
    {
        $this->em->remove($progress);
        $this->em->flush();

        return new JsonResponse([]);
    }
}
