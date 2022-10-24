<?php

namespace App\Http\Controller\Error;

use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Génère le body de la page d'erreur.
 */
class ErrorController extends AbstractController
{
    public function body(CourseRepository $courseRepository, TopicRepository $topicRepository): Response
    {
        return $this->render('bundles/TwigBundle/Exception/_body.html.twig', [
            'courses' => $courseRepository->findRandom(4),
            'topics' => $topicRepository->findRandom(5),
        ]);
    }

    /**
     * Simplifie l'affichage des erreurs dans l'environnement de test
     */
    public function test(?\Throwable $exception = null): Response
    {
        if (!$exception) {
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if ($exception instanceof \Exception) {
            $exception = FlattenException::create($exception);
        }
        return new Response($exception->getMessage(), $exception->getStatusCode(), $exception->getHeaders());
    }
}
