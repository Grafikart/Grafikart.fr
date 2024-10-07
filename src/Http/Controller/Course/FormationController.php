<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Repository\FormationRepository;
use App\Domain\History\HistoryService;
use App\Domain\History\Repository\ProgressRepository;
use App\Http\Controller\AbstractController;
use App\Http\Requirements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormationController extends AbstractController
{
    #[Route(path: '/formations', name: 'formation_index')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();

        return $this->render('formations/index.html.twig', [
            'formations' => $formations,
            'menu' => 'formations',
        ]);
    }

    #[Route(path: '/cursus', name: 'cursus_index')]
    public function tree(EntityManagerInterface $em): Response
    {
        $formations = collect($em->getRepository(Formation::class)->findBy([
            'slug' => [
                'php',
                'formation-javascript',
                'css',
                'html',
                'vuejs',
                'apprendre-sql',
                'git',
                'apprendre-algorithmique',
                'programmation-objet-php',
                'laravel',
                'apprendre-symfony-7',
                'upload-site',
                'react',
            ],
        ]))->keyBy(fn (Formation $f) => $f->getSlug() ?? '')->toArray();

        return $this->render('formations/tree.html.twig', [
            'formations' => $formations,
            'menu' => 'cursus',
        ]);
    }

    #[Route(path: '/formations/{slug:formation}', name: 'formation_show', requirements: ['slug' => Requirements::SLUG])]
    public function show(
        Formation $formation,
        ProgressRepository $progressRepository,
    ): Response {
        if ($formation->isForceRedirect() && $formation->getDeprecatedBy()) {
            $newFormation = $formation->getDeprecatedBy();

            return $this->redirectToRoute('formation_show', [
                'slug' => $newFormation->getSlug(),
            ], 301);
        }

        $user = $this->getUser();
        $progress = null;
        if ($user) {
            $progress = $progressRepository->findOneByContent($user, $formation);
        }

        return $this->render('formations/show.html.twig', [
            'formation' => $formation,
            'menu' => 'formations',
            'progress' => $progress,
        ]);
    }

    /**
     * Redirige vers le prochain chapitre à regarder.
     */
    #[Route(path: '/formations/{slug:formation}/continue', name: 'formation_resume', requirements: ['slug' => Requirements::SLUG])]
    public function resume(
        Formation $formation,
        HistoryService $historyService,
        EntityManagerInterface $em,
        NormalizerInterface $normalizer,
    ): RedirectResponse {
        $user = $this->getUser();
        $ids = $formation->getModulesIds();
        $nextContentId = $ids[0];
        if (null !== $user) {
            $nextContentId = $historyService->getNextContentIdToWatch($user, $formation) ?: $ids[0];
        }
        $content = $em->find(Course::class, $nextContentId);
        /** @var array $path */
        $path = $normalizer->normalize($content, 'path');

        return $this->redirectToRoute($path['path'], $path['params']);
    }
}
