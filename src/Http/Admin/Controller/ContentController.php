<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Entity\Content;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content", name="content_")
 */
final class ContentController extends BaseController
{
    /**
     * Endpoint pour récupérer le titre d'un cours ou d'une formation depuis son Id.
     *
     * @Route("/{id<\d+>}/title", name="title")
     */
    public function title(Content $content): JsonResponse
    {
        return new JsonResponse([
            'id' => $content->getId(),
            'title' => $content->getTitle(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="edit")
     */
    public function edit(Content $content): RedirectResponse
    {
        if ($content instanceof Formation) {
            $path = 'admin_formation_edit';
        } elseif ($content instanceof Course) {
            $path = 'admin_course_edit';
        } else {
            throw new NotFoundHttpException();
        }

        return $this->redirectToRoute($path, ['id' => $content->getId()]);
    }
}
