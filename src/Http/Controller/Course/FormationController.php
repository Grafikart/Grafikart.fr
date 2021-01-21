<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\FormationRepository;
use App\Domain\History\HistoryService;
use App\Domain\History\Repository\ProgressRepository;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormationController extends AbstractController
{
    /**
     * @Route("/formations", name="formation_index")
     */
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();

        return $this->render('formations/index.html.twig', [
            'formations' => $formations,
            'menu' => 'formations',
        ]);
    }

    /**
     * @Route("/cursus", name="cursus_index")
     */
    public function tree(EntityManagerInterface $em): Response
    {
        $technologies = $em->getRepository(Technology::class)->findBy([
            'slug' => ['php', 'ruby', 'nodejs', 'laravel', 'symfony', 'react', 'vuejs'],
        ]);
        $formations = collect($em->getRepository(Formation::class)->findBy([
            'slug' => [
                'php',
                'debuter-javascript',
                'css',
                'html',
                'webpack',
                'vuejs',
                'mysql',
                'git',
                'apprendre-algorithmique',
                'programmation-objet-php',
                'ruby-on-rails',
                'ruby',
                'nodejs',
                'laravel',
                'symfony-4-pratique',
                'upload-site',
                'react',
            ],
        ]))->keyBy(fn (Formation $f) => $f->getSlug())->toArray();

        return $this->render('formations/tree.html.twig', [
            'formations' => $formations,
            'technologies' => $technologies,
            'menu' => 'cursus',
        ]);
    }

    /**
     * @Route("/formations/{slug}", name="formation_show")
     */
    public function show(
        Formation $formation,
        ProgressRepository $progressRepository
    ): Response {
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
     *
     * @Route("/formations/{slug}/continue", name="formation_resume")
     */
    public function resume(
        Formation $formation,
        HistoryService $historyService,
        EntityManagerInterface $em,
        NormalizerInterface $normalizer
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
