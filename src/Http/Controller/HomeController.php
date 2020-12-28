<?php

namespace App\Http\Controller;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Cursus;
use App\Domain\Course\Entity\Formation;
use App\Domain\History\Entity\Progress;
use App\Domain\History\HistoryService;
use App\Domain\Live\Live;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $em;

    private HistoryService $historyService;

    public function __construct(EntityManagerInterface $em, HistoryService $historyService)
    {
        $this->em = $em;
        $this->historyService = $historyService;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->homeLogged($user);
        }
        $courseRepository = $this->em->getRepository(Course::class);

        return $this->render('pages/home.html.twig', [
            'menu' => 'home',
            'courses' => $courseRepository->findRecent(3),
            'hours' => round($courseRepository->findTotalDuration() / 3600),
            'formations' => $this->em->getRepository(Formation::class)->findRecent(3),
            'cursus' => $this->em->getRepository(Cursus::class)->findRecent(5),
            'lives' => $this->em->getRepository(Live::class)->findRecent(3),
            'posts' => $this->em->getRepository(Post::class)->findRecent(5),
        ]);
    }

    public function homeLogged(User $user): Response
    {
        $watchlist = $this->historyService->getLastWatchedContent($user);
        $excluded = array_map(fn (Progress $progress) => $progress->getContent()->getId(), $watchlist);
        $content = $this->em->getRepository(Content::class)
            ->findLatest(14)
            ->andWhere('c INSTANCE OF '.Course::class.' OR c INSTANCE OF '.Formation::class);
        if (!empty($excluded)) {
            $content = $content->andWhere('c.id NOT IN (:ids)')->setParameter('ids', $excluded);
        }

        return $this->render('pages/home-logged.html.twig', [
            'menu' => 'home',
            'latest_content' => $content,
            'watchlist' => $watchlist,
        ]);
    }
}
