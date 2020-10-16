<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\FormationRepository;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        ]);
    }

    /**
     * @Route("/formations/guide", name="formation_map")
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
        ]);
    }

    /**
     * @Route("/formations/{slug}", name="formation_show")
     */
    public function show(Formation $formation): Response
    {
        return $this->render('formations/show.html.twig', [
            'formation' => $formation,
        ]);
    }
}
